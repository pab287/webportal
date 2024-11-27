<div class="m-content">
  <div class="m-portlet m-portlet--mobile">
    <div class="m-portlet__head">
      <div class="m-portlet__head-caption">
        <div class="m-portlet__head-title">
            <h3 class="m-portlet__head-text">
                Sales
            </h3>
        </div>
      </div>
      <div class="m-portlet__head-tools">
        <button class="btn btn-info m-btn btnExportexcel" type="button" id="ExportPrint" data-toggle="m-tooltip" title="" data-original-title="PRINT">
          <span><i class="fa fa-print"></i></span>
        </button>
        <button class="btn btn-success m-btn btnExportexcel" type="button" id="ExportExcel" data-toggle="m-tooltip" title="" data-original-title="EXPORT EXCEL">
          <span><i class="fa fa-file-excel-o"></i></span>
        </button>
        <button class="btn btn-danger btnExportpdf" type="button" id="ExportPDF" data-toggle="m-tooltip" title="" data-original-title="EXPORT PDF">
          <span><i class="fa fa-file-pdf-o"></i></span>
        </button>
        <button class="btn btn-warning btnExportexcel" type="button" id="ExportCSV" data-toggle="m-tooltip" title="" data-original-title="EXPORT CSV">
          <span><i class="fa fa-file-excel-o"></i></span>
        </button>
      </div>
    </div>
    <div class="m-portlet__body">
      <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll mt-5">
          <div class="row">
              <div class="col-md-10">
                <div class="form-group m-form__group row">
                  <label class="col-1 col-form-label">DATE</label>
                  <div class="col-3">
                    <div class="input-group" id="date-picker">
                        <input type="text" class="form-control m-input"
                            placeholder="MMM DD, YYYY - MMM DD, YYYY" id="date-range"
                            name="date_range" data-validation="required" autocomplete='off'>
                        <span class="input-group-addon">
                            <i class="la la-calendar-check-o"></i>
                        </span>
                    </div>
                  </div>
                  <div class="col-4">
                    <button class="btn btn-info" onclick="generateReport()">
                      <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                    </button>
                  </div>
                </div>
              </div>
          </div>
          <table class="table table-striped table-bordered" id="tbl-sales_collection" width="100%" style="font-family: roboto;">
            <thead>
            <tr>
              <th>Payment Date</th>
              <th>Payment Reference#</th>
              <th>Type</th>
              <th>Penalty</th>
              <th>Reconnection Fee</th>
            </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tfoot>
          </table>
      </div>
        <!--::dt end::-->
    </div>
  </div>
</div>
