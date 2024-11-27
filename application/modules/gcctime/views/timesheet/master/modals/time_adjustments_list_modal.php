<style>
    tr {
        cursor: pointer;
    }
</style>

<div class="modal-header">
    <h5 class="modal-title">Time Adjustments List</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" id="time-adjustment-list-modal">
    <input type="hidden" id="timesheet_id" value="<?= $timesheet_id ?>">
    <input type="hidden" id="employee_id" value="<?= $employee_id ?>">

    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
            <p class="m--regular-font-size-lg3 m--font-bolder mb-0">
                <?= $employee->employee_name ?>
            </p>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 d-flex flex-row justify-content-end align-items-center">
            <p class="m--regular-font-size-lg3 m--font-bolder mb-0">
                <?= date("m/d/Y, D", strtotime($timesheet->date)) ?>
            </p>
        </div>
    </div>

    <div class="table-responsive-sm mt-4">
        <table class="table table-bordered table-hover"
               id="tbl-time-adjustments-list">
            <thead>
            <tr>
                <td colspan="2" class="font-weight-bold text-muted text-center">MORNING</td>
                <td colspan="2" class="font-weight-bold text-muted text-center">AFTERNOON</td>
                <td colspan="2" class="font-weight-bold text-muted text-center">MORNING</td>
                <td colspan="2" class="font-weight-bold text-muted text-center">AFTERNOON</td>
                <td colspan="5" class="font-weight-bold text-muted text-center">DETAILS</td>
            </tr>
            <tr>
                <td>IN</td>
                <td>OUT</td>
                <td>IN</td>
                <td>OUT</td>
                <td>SHIFT IN</td>
                <td>SHIFT OUT</td>
                <td>SHIFT IN</td>
                <td>SHIFT OUT</td>
                <td>LATE</td>
                <td>UT</td>
                <td>REG. HRS.</td>
                <td>OT</td>
                <td>STATUS</td>
            </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
</div>