<style>
  #pc_page .h-35_13 {
    height: 35.13px;
  }

  #pc_page .form-group .m-input {
      padding: 8.45px 16.25px;
  }

  /* Chrome, Safari, Edge, Opera */
  #pc_page input::-webkit-outer-spin-button,
  #pc_page input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox */
  #pc_page input[type=number] {
    -moz-appearance: textfield;
  }

  #tbl-payment_collection .m-checkbox > span:after{
    margin-left: -3px;
    margin-top: -8px;
  }
  #tbl-payment_collection .v-middle {
    vertical-align: middle!important;
  }

  #pc_page #pc_inputs_wrapper .col-6 {
    max-width: 49.5%;
  }

  #pc_page #pc_inputs_wrapper .col-6 .row .col-5 {
    max-width: 40%;
  }

  #pc_page #pc_inputs_wrapper .col-6 .row .col:last-child {
      max-width: 12%;
      flex: 0 0 12%;
  }

  #pc_page #pc_inputs_wrapper .col-6 .row .col:not(:last-child) {
    max-width: 28%;
    flex: 0 0 28%;
  }

  #pc_page #pc_inputs_wrapper .col-6 .row.alert {
    padding: 15px;
  }

  #pc_page #pc_inputs_wrapper label {
    text-transform: uppercase;
    font-weight: 500;
  }

  #pc_page .help-block.form-error {
    display: none;
  }
</style>

<div id="pc_page" class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Payment Collection
                    </h3>
                </div>
            </div>

            <div class="m-portlet__head-tools">
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
            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                <div id="pc_inputs_wrapper" class="row justify-content-between align-items-end mb-0 mx-0">
                    <div class="col-6 px-0">
                        <div class="row justify-content-between align-items-end alert m-alert m-alert--default mx-0 mb-0">
                            <div class="col-5 p-0">
                                <label class="mb-2">Employee: </label>
                                <select name="employee" id="employee" class="form-control" data-validation="required"></select>
                            </div>

                            <div class="col-5 p-0">
                                <label class="mb-2">Date: </label>
                                <div class="input-group" id="date-picker">
                                    <input type="text" class="form-control m-input" placeholder="MMM DD, YYYY - MMM DD, YYYY" id="date-range" name="date_range" data-validation="required" autocomplete='off' style="height: 35.13px;">
                                    <span class="input-group-addon bg-white"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>

                            <div class="col-2 p-0">
                                <button class="btn btn-info w-100" onclick="generateReport()">
                                  <span><i class="fa fa-gears pr-2"></i>GENERATE</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <table class="table table-striped table-bordered" id="tbl-payment_collection" width="100%" style="font-family: roboto;">
                    <thead>
                        <tr>
                            <th width="18%">Account</th>
                            <th>Bill #</th>
                            <th width="5%">AR #</th>
                            <th>Payment #</th>
                            <th width="5%">Type</th>
                            <th title="Receive Payment" width="5%">Amount</th>
                            <th width="8%">Bal. Covered</th>
                            <th width="10%" title="Payment Date">Payment Date</th>
                            <th width="10%" title="Applied Payment Date">AP Date</th>
                            <th width="15%">Collected By</th>
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
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>