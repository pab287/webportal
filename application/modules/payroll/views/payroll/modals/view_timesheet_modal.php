<div class="modal fade" id="view-timesheet-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span style="font-weight: 600;">TIMESHEET</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><em class='fa fa-flag' style='color:#ffb822;'></em><span class="m--font-bold"> Holiday</span>&nbsp;&nbsp;&nbsp;
                <em class='fa fa-times-rectangle' style='color:#5c5d62;'></em><span class="m--font-bold"> Absent</span>&nbsp;&nbsp;&nbsp;
                <em class='fa fa-clock-o' style='color:#5867dd;'></em><span class="m--font-bold"> Overtime</span></p>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>DATE</th>
                        <th>DAY</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>IN</th>
                        <th>OUT</th>
                        <th>LATE</th>
                        <th>UT</th>
                        <th>REG. HRS.</th>
                        <th>OT</th>
                        <th>NDOT</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary m-btn m-btn--icon btnPrint"
                        onclick="printTimesheet(this)">
                    <span>
                        <i class="fa fa-print mr-1"></i> PRINT
                    </span>
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>