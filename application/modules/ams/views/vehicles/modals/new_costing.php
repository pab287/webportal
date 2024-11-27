<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <form action="<?=base_url("ams/vehicles/save_costing")?>" id="frm-add-costing">
            <div class="modal-header">
                <h5 class="modal-title">Add Cost</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group m-form__group">
                    <label>Description</label>
                    <input type="text" class="form-control m-input input-auto-height" autocomplete="off"
                           name="description" data-validation="required">
                </div>

                <div class="row">
                    <div class="col-xl-6 col-sm-12">
                        <div class="form-group m-form__group mt-2">
                            <label>Price</label>
                            <div class="input-group m-form__group">
                                <span class="input-group-addon">&#8369;</span>
                                <input type="text" class="form-control input-auto-height text-right m--font-bolder font-weight-bold"
                                       autocomplete="off" name="price" value="0.00" data-validation="required">
                            </div>
                        </div>
                    </div>
                    <div class="offset-xl-6 offset-sm-0"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btnNew">
                    Save
                </button>
                <button type="button" class="btn btn-danger btnNew" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $("input[name='price']")
        .maskMoney({thousands: ',', decimal: '.', allowZero: true});
</script>