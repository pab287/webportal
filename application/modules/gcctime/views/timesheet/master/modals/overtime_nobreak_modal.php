<div class="modal fade" tabindex="-1" role="dialog"
     id="overtime-nobreak-modal">
    <div class="modal-dialog modal-xl" role="document">
        <form id="frm-add-shift-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Overtime Records</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12 col-sm-12">
                            <h6>Tag Employee/s timesheet record as no overtime break!</h6>
                        </div>
                    </div>
                    <div class="table-responsive-sm mt-2">
                        <table class="table table-bordered" id="tbl-overtime--nobreak" width="100%">
                            <col width="3%">
                            <col width="12%">
                            <col width="*">
                            <col width="17%">
                            <col width="17%">
                            <col width="8%">
                            <col width="10%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Date</th>
                                    <th>Employee Name</th>
                                    <th>Overtime In</th>
                                    <th>Overtime Out</th>
                                    <th>OT HRS</th>
                                    <th>NDIFF HRS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btnSave btnUpdateTsNoBreak">Update</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </form>
    </div>
</div>