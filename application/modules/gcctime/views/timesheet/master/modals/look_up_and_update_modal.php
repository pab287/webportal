<div class="modal fade" tabindex="-1" role="dialog"
     id="look-up-and-update-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Look Up & Update Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                        <span class="m--regular-font-size-lg1 m--font-bolder text-muted">BIOMETRIC NO: </span>
                        <span class="m--regular-font-size-lg1 m--font-boldest" id="biometric-no"></span>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 form-group">
                        <input type="text" class="form-control" placeholder="Search..."
                               id="search" autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive mt-2">
                    <table width="100%" class="table table-bordered">
                        <thead>
                        <tr>
                            <th>BIOMETRIC NO.</th>
                            <th>EMPLOYEE NAME</th>
                            <th>ACTIONS</th>
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