<style>
.group td {
    background-color: #233e6b !important;
    color: #ffffff;
    border: 1px solid #233e6b !important;
}
</style>
<div id="app_users" class="m-content">
	<div class="row">
        <div class="col-3 col-md-3 col-lg-3 col-xl-3 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Filter By</h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <form action="" class="m-form" id="frm-filter">
                        <div class="form-group m-form__group pb-0">
                            <label for="date-range" class="m-form__label required">Date</label>
                            <div class="input-group" id="date-picker">
                                <input type="text" class="form-control m-input" readonly="" placeholder=""
                                       name="date-range" id="date-range" data-validation="required">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="company" class="m-form__label required">Company</label>
                            <select name="company" id="company" class="form-control" data-validation="required">
                                <option></option>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="payroll_group" class="m-form__label">
                                PAYROLL GROUP
                                <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                            </label>
                            <select class="form-control" id="payroll_group" multiple></select>
                        </div>

                        <div class="form-group m-form__group pb-0">
                            <label for="employees">Employee</label>
                            <select name="employees[]" id="employees" class="form-control select2-multiple-custom" multiple></select>
                        </div>

                        <div class="d-flex flex-row justify-content-end mt-4">
                            <button type="button" class="btn btn-warning m-btn m-btn--icon m-btn--pill btnAdvance_search m-btn--sm mr-1 text-white" onclick="resetFilter(this)">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Reset Filter</span>
                            </span>
                            </button>
                            <button type="submit" class="btn btn-info m-btn m-btn--icon m-btn--pill btnAdvance_search m-btn--sm">
                            <span>
                                <i class="fa fa-search"></i>
                                <span>Find</span>
                            </span>
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
		<div class="col-9 col-md-9 col-xl-9 col-lg-9 col-sm-12">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Mobile Attendance Logs
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="mobile_attendance_logs" style="width:100%">
							<thead>
								<tr>
                                    <th>App User</th>
                                    <th>Biometric No</th>
									<th>Timestamp</th>
									<th>Date</th>
									<th>Time</th>
									<th>Status</th>
									<th>In Location</th>
									<th>Location Address</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
