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
                <form id="frm-filter-payroll-regular_ndiff" class="m-form">
				<div class="m-portlet__body">
                    <div class="row">
                        <div class="form-group m-form-group col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            <label for="company" class="required">COMPANY</label>
                            <select name="company" id="company" class="form-control" data-validation="required">
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
                                Night Differential <small>Regular</small>
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">&nbsp;</div>
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
                    </div>
                    <div class="table-responsive-sm">
                        <table class="table table-bordered"
                               id="tbl-payroll-night-differential"
                               style="width: 100%">
                            <thead>
                            <tr>
                                <th scope="col">Id Number</th>
                                <th scope="col">Employee Name</th>
                                <th scope="col">Company</th>
                                <th scope="col">Reg. Ndiff.</th>
                                <th scope="col">Last Updated By</th>
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