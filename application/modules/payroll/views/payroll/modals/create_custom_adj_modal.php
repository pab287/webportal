<div class="modal fade" tabindex="-1" role="dialog"
     id="manage-custom-adjustments-modal">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Custom Adjustments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="frm-manage-custom-adjustments"
                      data-mode="add">
                    <input type="hidden" name="id">
                    <input type="hidden" name="payroll_sheet_id">
                    <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">

                    <div class="row">
                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 form-group">
                            <label for="" class="required">Particular</label>
                            <input type="text" class="form-control" name="particulars" data-validation="required">
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 form-group">
                            <label for="" class="required">Amount</label>
                            <input type="text" class="form-control text-right" name="amount"
                                   data-validation="required">
                        </div>
                    </div>

                    <div class="form-group mt-1">
                        <label for="">DESCRIPTION</label>
                        <textarea name="description" id="" rows="2" class="form-control"></textarea>
                    </div>

                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-primary m-btn btnSave">SAVE</button>
                        <button type="button" class="btn btn-danger m-btn btnCancel"
                                onclick="resetManageCustomAdjustmentsForm()">RESET
                        </button>
                    </div>
                </form>

                <div class="table-responsive mt-5">
                    <table class="table table-bordered" width="100%">
                        <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Amount</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog"
     id="modal-confirm-delete-custom-adjustment">
    <form id="frm-confirm-delete-custom-adjustment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span style="font-weight: 600;">CONFIRM DELETE</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="m--regular-font-size-lg1"
                       style="font-size: 14px !important; font-weight: 500;">
                        Are you sure to delete selected custom adjustment?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btnDelete">YES</button>
                    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">NO</button>
                </div>
            </div>
        </div>
    </form>
</div>