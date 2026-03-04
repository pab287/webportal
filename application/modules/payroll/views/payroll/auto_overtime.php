<style>
@media (min-width: 768px) {
  .modal-full {
    width: 100%;
   	max-width:90%;
  }
}

</style>
<div class="m-content">
	<div class="row">
        <div class="col-3 col-md-3 col-lg-3 col-xl-3 col-sm-12">
            <div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <i class="fa fa-filter"></i>
                            </span>
							<h3 class="m-portlet__head-text">
                                Filter
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
                <form id="frm-filter-payroll-auto_overtime" class="m-form">
                    <div class="m-portlet__body">
                        <div class="row">
                            <div class="form-group m-form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="company" class="m--font-bolder">STATUS</label>
                                <select name="status" id="status" class="form-control">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group m-form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="company" class="m--font-bolder">COMPANY</label>
                                <select name="company" id="company" class="form-control">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label class="m--font-bolder" for="payroll_group">PAYROLL GROUP <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                </label>
                                <select class="form-control" id="payroll_group" multiple="multiple"></select>
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label class="m--font-bolder" for="employees">EMPLOYEE/S <span class="m-form__help p-0" style="text-transform: none; font-width: 600;">(Optional)</span>
                                </label>
                                <select name="employees[]" id="employees" class="form-control"
                                multiple="multiple"></select>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot text-right">
                        <button type="button"
                            class="btn btn-warning m-btn btnAdvance_search m-btn--sm mr-1 text-white"
                            onclick="resetFilter(this)">
                            <span>
                                <i class="fa fa-refresh"></i>
                                <span>Reset Filter</span>
                            </span>
                        </button>
                        <button type="submit" class="m-btn btn btn-success btnAdvance_search btn-submit"> GO </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-9 col-xl-9 col-lg-9 col-md-9 col-sm-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Auto Overtime
							</h3>
						</div>
					</div>
                    <div class="m-portlet__head-tools">
	<ul class="m-portlet__nav">
		<li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="click" aria-expanded="false">
			<a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
				<i class="la la-ellipsis-h m--font-brand"></i>
			</a>
			<div class="m-dropdown__wrapper">
				<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 22.5px;"></span>
				<div class="m-dropdown__inner">
					<div class="m-dropdown__body">
						<div class="m-dropdown__content">
							<ul class="m-nav">
								<li class="m-nav__section m-nav__section--first">
													<span class="m-nav__section-text">
														Quick Action
													</span>
												</li>
												<li class="m-nav__item">
													<a href="javascript:void(0)" class="m-nav__link btnQuick_action" data-toggle="modal" data-target="#approve_overtime">
														<i class="m-nav__link-icon flaticon-open-box"></i>
														<span class="m-nav__link-text">
															Approve Overtime
														</span>
													</a>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>
				</div>
				</div>
				<div class="m-portlet__body">
                    <!-- <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1 exportDropdown">&nbsp;</div>
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right d-flex flex-row">
                            <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span>
                                        <i class="la la-search"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div> -->
                    <div class="table-responsive-sm">
                        <table class="table table-bordered"
                               id="tbl-employee-auto-overtime"
                               style="width: 100%">
                            <thead>
                            <tr>
                                <th scope="col">Id Number</th>
                                <th scope="col">Employee Name</th>
                                <th></th>
                                <th></th>
                                <th scope="col">Company</th>
                                <th scope="col">Auto Overtime</th>
                                <th scope="col">Last Updated By</th>
                                <th></th>
                                <th scope="col">Action</th>
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

<div class="modal fade" id="approve_overtime" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div id="modalTempContainer" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Approve Overtime Request</h5>
                <button type="button" class="close modalClose" aria-label="Close" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="approve_overtime_form">
                <div class="modal-body">
                    <div class="alert alert-warning text-dark" role="alert">
                        <strong>Important:</strong><br/>
                        This action will <b>approve all pending overtime requests</b> for employees with 
                        <b>Auto Overtime enabled</b> within the selected date range.  
                        This process cannot be undone.
                    </div>
                    <div class="form-group">
                        <label for="approve_overtime_daterange" class="font-weight-bold required">
                            Select Date Range
                        </label>
                        <input type="text" class="form-control" id="approve_overtime_daterange" placeholder="Select date range" data-validation="required" autocomplete="off" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btnSave">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>