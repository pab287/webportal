<style>
    .tree-dropdown-trigger .help-block {
        display: none;
    }
</style>

<div class="modal-dialog" role="document">
    <form action="" id="frm-add-rate">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span style="text-transform: none">ADD RATE for </span>
                    <span class="m--font-boldest"><?= $label ?></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                <input type="hidden" name="item_id" value="<?= $id ?>">

                <div class="form-group">
                    <label for="">Item</label>
                    <input type="text" value="<?= $label ?>" disabled class="form-control">
                </div>

                <div class="row pt-3">
                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                        <div class="form-group m-form-group">
                            <label for="tariff">Tariff *</label>
                            <div class="input-group m-input-group">
                                <spann class="input-group-addon">&#8369;</spann>
                                <input type="text" name="tariff" class="form-control text-right money" id="tariff"
                                       data-validation="required">
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
                                    <option value="<?= $unit->id ?>">
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
                        <input type="text" id="category-text" class="form-control" name="category_text" readonly />
                        <input type="hidden" id="category-id" name="category_id" />
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
    var validateNewItemRate = function () {
        $.validate({
            form: "#frm-add-rate",
            lang: "en",
            scrollToTopOnError: false,
            onSuccess: function (el) {
                const form = $(el);
                $.ajax({
                    url: baseUrl("pms/rates/add_item_rate"),
                    type: "POST",
                    dataType: "JSON",
                    data: form.serialize(),
                    success: function (response) {
                        if (response) {
                            toastr.success("Item rate was successfully saved.", "Rate Added.", 10000);
                            dtItemRate.ajax.reload();
                            form.resetForm();
                            modalContainer.modal("hide");
                        } else {
                            toastr.error("An error occurred.", "Error Adding Rate", 10000);
                        }
                    },
                    error: function (xhr, status, error) {
                        const err = eval("(" + xhr.responseText + ")");
                        toastr.warning(err, "Error Adding Rate", 10000);
                    }
                });
                return false;
            }
        });
    }

    jQuery(document).ready(function () {
        validateNewItemRate();
    });
</script>