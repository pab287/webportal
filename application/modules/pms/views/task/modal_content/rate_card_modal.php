<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Rate Card</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div id="tree-view"></div>

            <div class="d-flex flex-row align-items-center mb-1 mt-5">
                <p class="mb-0 m--font-bolder text-muted mr-3">Updates</p>
                <button type="button" class="btn btn-sm btn-primary m-btn m-btn--icon"
                        style="height: 30px; line-height: 1em;"
                        onclick="openUpdateHistoryModal(<?=$checklist_priv_id?>)">
                    <span>
                        <i class="la la-history"></i>
                        <span>Update's History</span>
                    </span>
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="tbl-rate-card-updates"
                       width="100%">
                    <thead>
                    <tr>
                        <th>Item</th>
                        <th id="update-header">
                            UPDATES
                        </th>
                        <th>Action</th>
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
</div>