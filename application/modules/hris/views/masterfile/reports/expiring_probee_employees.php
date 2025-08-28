<style>
    #table-expiring-probees tbody * {
        font-size: 12px;
    }
</style>

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Expiring Probationary Employees
                    </h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <button class="btn btn-success m-btn" id="ExportExcel" data-toggle="m-tooltip" title="" data-original-title="EXPORT EXCEL">
                    <span><i class="fa fa-file-excel-o"></i></span>
                </button>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped" id="table-expiring-probees" width="100%">
                    <thead>
                    <tr>
                        <th>Company</th>
                        <th>Position</th>
                        <th>ID #</th>
                        <th>Name</th>
                        <th>Immediate Head</th>
                        <th>Date Hired</th>
                        <th>3rd Month</th>
                        <th>5th Month</th>
                        <th>End of probationary</th>
                        <th title="Days until evaluation">Days Eval.</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>