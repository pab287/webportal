<div class="modal-dialog modal-extra-lg" role="document">
    <!--<form action="" id="frm-rate-update-history">-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span style="text-transform: none">UPDATE HISTORY for </span>
                    <span class="m--font-boldest"><?= $label ?></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rate-id" value="<?= $rate_id ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tbl-rate-update-history"
                           width="100%">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>Tariff</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Approved/DECLINED By</th>
                            <th>DATE</th>
                            <th>Encoded By</th>
                            <th>Date Encoded</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    <!--</form>-->
</div>