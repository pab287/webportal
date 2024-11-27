<div class="m-content">
	<div class="row">
		<div class="col-lg-4">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								New Rental details
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
                </div>
                <form id="frmRentalForm" action="<?php echo site_url('eforms/tripping/save_rental'); ?>" class="m-form m-form--fit m-form--label-align-right">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="m-portlet__body">
                    <div class="form-group m-form__group">
						<label>
							Reference No
						</label>
						<input class="form-control m-input" type="text" name="reference_no" data-validation="required">
					</div>
                    <div class="form-group m-form__group">
						<label>
							Date
						</label>
						<div class="input-group date" id="m_datepicker_rental">
                            <input type="text" class="form-control m-input" readonly="" name="date" placeholder="Select date">
                            <span class="input-group-addon">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
					</div>
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Select Driver
						</label>
						<select class="form-control m-input m-input--square" name="driver" id="driversSelect" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="driversUnitSelect">
							Select Vehicle/Unit
						</label>
						<select class="form-control m-input m-input--square" id="driversUnitSelect" name="unit" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label>
							Hours Rendered
						</label>
						<input class="form-control m-input" type="text" name="hours" data-validation="required">
					</div>
                </div>
                <div class="m-portlet__foot">
                    <div class="row align-items-center">
                        <div class="col-lg-6 m--valign-middle">
                           
                        </div>
                        <div class="col-lg-6 m--align-right">
                            <button type="submit" class="btn btn-success btnSave">
                                Submit
                            </button>
                            <button type="button" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
    </div>
</div>