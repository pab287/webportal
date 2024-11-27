<style>
  .dataTables_wrapper .dt-buttons {
  float: right;
}
</style>
<div class="m-content">
  <div class="row">
  <div class='col-lg-12'>
  <div class="m-portlet m-portlet--mobile">
  <div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Overtime Summary
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
  <div class="m-portlet__body">
  
  <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
  <div class="align-items-center">
  <form id = "reportsForm">
                        <div class="col-md-12 row">
                            <div class="form-group m-form__group " style="margin-left: 10px; width: 30%;">
                                <label class="mb-1" style="font-weight: 600;">Company</label>
                                <select name="company" id="company"
                                        class="form-control"><option></option></select>
                            </div>

                            <div class="form-group m-form__group" style="width: 30%; margin-left: 10px; align-self: end;">
                            <button type="submit" id="btnsubmit" class="btn btn-success">Generate</button>
                            </div>
                            <div class="form-group m-form__group ml-3" style="width: 20%;">
                                <label class="mb-1" style="font-weight: 600;">Date Range</label>
                                <input type = "text" name = "data_time" id = "date_time"
                                        class="form-control" placeholder = "Select Date"/>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                        <div class="form-group m-form__group" style="width:90%; margin-left: 10px;">
                                <label class="mb-1" style="font-weight: 600;">Payroll Group</label>
                                <select name="payroll_group[]" id="payroll_group"
                                        class="form-control" multiple="multiple"><option></option></select>
                            </div>
                            <div class="form-group m-form__group" style="margin-left: 50px; align-self: end;">
                            <button type="button" id="btnreset" class="btn btn-warning btnReset btnAdvance_search" onclick="resetFilter(this)"><span><i class="fa fa-refresh "></i> RESET</span></button>
                            </div>
                        </div>
                        <div class="col-md-12 row">
                        <div class="form-group m-form__group " style="width:100%; margin-left: 10px;">
                                <label class="mb-1" style="font-weight: 600; ">Employee</label>
                                <select name="employee[]" id="employee"
                                        class="form-control" multiple="multiple"><option></option></select>
                            </div>
                        </div>
                        </form>

                    </div>
                        <table class="table table-striped table-bordered" id="table-overtime-report" width="100%">
                            <thead>
                                <tr>
                                    <th>Reference No</th>
                                    <th>Employee</th>
                                    <th>Company</th>
                                    <th>Purpose</th>
                                    <th>Actual Time In</th>
                                    <th>Actual Time OUT</th>
                                    <th>GPS IN</th>
                                    <th>GPS OUT</th>
                                    <th>Total OT Hours</th>
                                </tr>
                            </thead>
                            <tbody>	
                            </tbody>
                        </table>
                    </div>

  </div>


  </div>
  </div>
  </div>
</div>