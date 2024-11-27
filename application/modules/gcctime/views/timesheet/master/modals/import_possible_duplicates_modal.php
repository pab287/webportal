<div class="modal fade" role="dialog"
     id="import-timesheet-possible-duplicates-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title m--font-bolder">Import Successful!</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m--font-boldest m--regular-font-size-lg1">
                    Timesheet was successfully imported, however there are conflict in timesheet record(s) detected.
                </div>

                <div class="m--margin-top-30 d-flex flex-row align-items-center">
                    <div class="flex-grow-0 flex-shrink-0 mr-3">
                        <i class="fa fa-warning m--font-warning"
                           style="font-size: 24px;"></i>
                    </div>
                    <div class="flex-grow-1 flex-shrink-0">
                        <div class="m--font-boldest">
                            <span style="border-bottom: 1px dotted grey;"
                                  class="m--font-danger">
                                CONFLICTS DETECTED.
                            </span>
                        </div>
                        <div class="m--font-bolder m--regular-font-size-sm1 m--font-info">
                            NOTE: TICK THE GREEN CHECK BUTTON FOR THE CHANGES YOU WANT TO ACCEPT.
                        </div>
                    </div>
                </div>

                <div class="table-responsive m--margin-top-5">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>CURRENT</th>
                            <th>CHANGES</th>
                            <th></th>
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
</div>