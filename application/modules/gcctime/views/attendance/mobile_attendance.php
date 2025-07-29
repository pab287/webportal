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
                            <label for="date-range">Date</label>
                            <div class="input-group" id="date-picker">
                                <input type="text" class="form-control m-input" readonly="" placeholder=""
                                       name="date-range" id="date-range">
                                <span class="input-group-addon">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group m-form__group pb-0">
                            <label for="employees">Employee</label>
                            <select name="employees[]" id="employees"
                                    class="form-control select2-multiple-custom" multiple></select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="company">Company</label>
                            <select name="company" id="company" class="form-control">
                                <option></option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?= $company->id ?>"><?= $company->text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group m-form__group">
                            <label for="payroll_group">
                                PAYROLL GROUP
                                <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                            </label>
                            <select class="form-control" id="payroll_group" multiple></select>
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
					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1"></div>
							<div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
								<div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
									<input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
									<span class="m-input-icon__icon m-input-icon__icon--left">
										<span>
											<i class="la la-search"></i>
										</span>
									</span>
								</div>
								<div class="m-separator m-separator--dashed d-xl-none"></div>
							</div>
						</div>
						<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
							<div class="row align-items-center">
								<div class="col-xl-8 order-2 order-xl-1"></div>
							</div>
						</div>
					</div>
					<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
						<table class="table table-striped table-bordered" id="mobile_attendance_logs" style="width:100%">
							<thead>
								<tr>
									<th>App User</th>
									<th>Date</th>
									<th>Time</th>
									<th>Status</th>
									<th>Actions</th>
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
