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
                    <form class="m-form" id="frm-filter" action="<?php echo base_url('gcctime/attendance/get_mobile_attendance_data'); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group m-form__group pb-0">
                            <label for="date-range" class="m-form__label required">Date</label>
                            <div class="input-group" id="date-picker">
                                <input type="text" class="form-control m-input" readonly="" placeholder=""
                                       name="date_range" id="date-range" data-validation="required">
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
                    <div id="statusFilter" class="mb-2 m-animate-fade-in" v-if="count > 0">
                        <div class="m-form__group form-group row">
                            <label for="" class="col-2 col-form-label">
                                Site Location Status
                            </label>
                            <div class="col-10">
                                <div class="m-radio-inline">
                                    <label class="m-radio">
                                        <input type="radio" value="all" v-model="status">
                                        All
                                        <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input type="radio" value="Yes" v-model="status">
                                        <label for="" class="m--font-success">Inside Location</label>
                                        <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input type="radio" value="No" v-model="status">
                                        <label for="" class="m--font-danger">Outside Location</label>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="modal fade" id="modal-preview-mobile_attendance" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Attendance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="preview-mobile_attendance" class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="m-widget3">
                                <div class="m-widget3__item">
                                    <div class="m-widget3__header">
                                        <div class="m-widget3__info pl-0">
                                            <p class="m-widget3__username mb-0"><span v-text="row.employee_name">&nbsp;</span>
                                                <span class="ml-5 m--font-primary" v-text="row.biometricno">&nbsp;</span>
                                            </p>
                                            <p class="m-widget3__time" v-text="dateTimeFormatter()">&nbsp;</p>
                                        </div>
                                    </div>
                                    <div class="m-widget3__body">
                                        <p class="m-widget3__text" v-text="row.address">&nbsp;</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <table class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th class="text-center">Punch Status</th>
                                        <th class="text-center">In Location</th>
                                        <th>Longitude</th>
                                        <th>Latitude</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td v-text="row.date">&nbsp;</td>
                                        <td v-text="row.time">&nbsp;</td>
                                        <td class="text-center" v-text="row.time_status">&nbsp;</td>
                                        <td class="text-center">
                                            <span class="m--font-boldest" :class="row.in_location == 'Yes' ? 'text-success' : 'text-danger'"
                                            v-text="row.in_location">&nbsp;</span>
                                        </td>
                                        <td v-text="row.longtitude">&nbsp;</td>
                                        <td v-text="row.latitude">&nbsp;</td>
                                    </tr>
                            </table>
                        </div>
                        <div class="col-12">
                            <div class="m-animate-fade-in" ref="googleMap" style="width: 100%; height: 400px;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
