<style>
    .tree-dropdown-trigger .help-block {
        display: none;
    }
</style>

<div class="modal-dialog" role="document">
    <form action="" id="frm-edit-rate">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span style="text-transform: none">EDIT RATE for </span>
                    <span class="m--font-boldest"><?= $label ?></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="id" value="<?= $rate_id ?>">

                <div class="form-group">
                    <label for="">Item</label>
                    <input type="text" value="<?= $label ?>" disabled class="form-control">
                </div>

                <div class="row pt-3">
                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                        <div class="form-group m-form-group">
                            <label for="tariff">Tariff *</label>
                            <div class="input-group m-input-group">
                                <span class="input-group-addon">&#8369;</span>
                                <input type="text" name="tariff" class="form-control text-right money" id="tariff"
                                       data-validation="required"
                                       value="<?= number_format($details->tariff, 2, ".", ",") ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-7 col-md-7 col-sm-12">
                        <div class="form-group">
                            <label for="unit">Unit *</label>
                            <select name="unit" id="unit" class="form-control"
                                    data-validation="required">
                                <option value=""></option>
                                <?php foreach ($units as $unit): ?>
                                    <option <?= $unit->id === $details->unit ? "selected" : "" ?>
                                            value="<?= $unit->id ?>">
                                        <?= $unit->uom_desc ?> (<?= $unit->uom_code ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="tree-dropdown-container pt-3">
                    <div class="form-group tree-dropdown-trigger mb-1">
                        <label for="" id="category-label">Category</label>
                        <input type="text" id="category-text" class="form-control" name="category_text"  readonly />
                        <input type="hidden" id="category-id" name="category_id" value="<?= $details->category_id ?>" />
                    </div>
                    <div id="category-tree" class="tree"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    var validateEditRate = function () {
        $.validate({
            form: "#frm-edit-rate",
            lang: "en",
            scrollToTopOnError: false,
            onSuccess: function (el) {
                const form = $(el);
                $.ajax({
                    url: baseUrl("pms/rates/edit_item_rate"),
                    type: "POST",
                    dataType: "JSON",
                    data: form.serialize(),
                    success: function (response) {
                        const status = parseInt(response);
                        if (status === 1) {
                            toastr.success("Item rate was successfully update.", "Rate Updated.", 10000);
                            dtItemRate.ajax.reload();
                            form.resetForm();
                            modalContainer.modal("hide");
                        } else if (status === 2) {
                            toastr.warning("No changes was detected.", "Noting to Update.", 10000);
                            modalContainer.modal("hide");
                        } else {
                            toastr.error("An error occurred.", "Error Updating Rate", 10000);
                        }
                    },
                    error: function (xhr, status, error) {
                        const err = eval("(" + xhr.responseText + ")");
                        toastr.warning(err, "Error Updating Rate", 10000);
                    }
                });
                return false;
            }
        });
    }

    jQuery(document).ready(function () {
        validateEditRate();
    });
</script>