<style>
    .span-link {
        cursor: pointer;
        color: #5867dd;
        transition: border-bottom 150ms ease-out;
    }

    .span-link:hover {
        border-bottom: 1px dotted #5867dd;
    }
</style>

<div class="modal fade" tabindex="-1" role="dialog" id="pi-history-modal">
    <div class="modal-dialog modal-lg" role="document"
         style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Physical Inventory History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 form-group">
                        <label for="sku">SKU</label>
                        <input type="text" class="form-control" disabled id="sku">
                    </div>
                    <div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 form-group">
                        <label for="description">ITEM DESCRIPTION</label>
                        <input type="text" class="form-control" disabled id="description">
                    </div>
                </div>

                <div class="table-responsive-sm mt-3">
                    <table class="table table-bordered table-striped"
                           id="tbl-pi-history" width="100%">
                        <thead>
                        <tr>
                            <th>PI. REF. NO.</th>
                            <th class="text-center">VARIANCE</th>
                            <th>REMARKS</th>
                            <th>CREATED BY</th>
                            <th>APPROVED BY</th>
                            <th>TRANSACTIONS</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
