<div class="modal-header">
<h5 class="modal-title" id="exampleModalLabel">Task Quantity Request</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
</button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label>Task Name</label>
                <div class="row">
                    <div class="col-7">
                        <select class="form-control form-control" id="task_name"></select>
                    </div>
                    <div class="col-5 text-right">
                        <button type="button" class="btn btn-success btnNew" onclick="getAllAvailableTask()"><i class="la la-plus" /> Add All Available Task</button>
                        <button type="button" class="btn btn-danger btnDelete" onclick="clearAllTask()"><i class="la la-trash" /> Clear All Task</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <form id="frmSetTaskRequestQty">
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                    <table class="table table-striped table-bordered" id="table-task_requested_qty" width="100%">
                        <col width="*" />
                        <col width="15%" />
                        <col width="8%" />
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary btnSave" onclick="submitRequestQty()">Submit Request</button>
    <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Cancel</button>
</div>