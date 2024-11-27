<div class="m-content">
	<div class="row">

		<div class="col-lg-4">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile m-portlet--full-height">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Billing Rate
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
                </div>
                <form class="m-form m-form--fit" id="frmRate" method="POST" action="<?php echo site_url();?>eforms/billing/create_rate">
                    <div class="m-portlet__body">
                        <input type="hidden" name="id" value="id">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row m--margin-bottom-10">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-4 col-form-label">
                                        Rate * :
                                    </label>
                                    <div class="col-8">
                                        <input type="text" name="rate" class="form-control m-input rate text-right" data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                            <button type="submit" class="btn btn-primary btnSave">
                                Save
                            </button>
                            <button type="reset" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- <div class="col-lg-4">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Interest
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<div class="m-portlet__body">

                </div>
            </div>
        </div> -->

        <!-- <div class="col-lg-4">
			<div class="m-portlet m-portlet--mobile m-portlet--full-height">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Print count limit
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<form class="m-form m-form--fit" id="frmLimitPrint" method="POST" action="<?php echo site_url();?>eforms/billing/update_print_limit">
                    <div class="m-portlet__body">
                        <input type="hidden" name="id" value="id">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row m--margin-bottom-10">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-4 col-form-label">
                                        Limit * :
                                    </label>
                                    <div class="col-8">
                                        <input type="text" name="limit" class="form-control m-input rate text-right" data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                            <button type="submit" class="btn btn-primary btnSave">
                                Save
                            </button>
                            <button type="reset" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div> -->

        <div class="col-lg-4">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile m-portlet--full-height">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Reconnection fee
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<form class="m-form m-form--fit" id="frmReconnectionFee" method="POST" action="<?php echo site_url();?>eforms/billing/update_reconnection_fee">
                    <div class="m-portlet__body">
                        <input type="hidden" name="id" value="id">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row m--margin-bottom-10">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-4 col-form-label">
                                        Amount * :
                                    </label>
                                    <div class="col-8">
                                        <input type="text" name="reconnection_amount" class="form-control m-input rate text-right" data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                            <button type="submit" class="btn btn-primary btnSave">
                                Save
                            </button>
                            <button type="reset" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile m-portlet--full-height">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Penalty
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
                <form class="m-form m-form--fit" id="frmPenaltyDetails" method="POST" action="<?php echo site_url();?>eforms/billing/update_penalty_details">
                <input type="hidden" name="id" value="id">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <label class="col-4 col-form-label">
                            Type:
                        </label>
                        <div class="col-8">
                            <select class="form-control m-input m-input--square" name="type" data-validation="required">
                                <option value="" selected disabled hidden>Select option</option>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-4 col-form-label">
                            Amount * :
                        </label>
                        <div class="col-8">
                            <input type="text" name="amount" class="form-control m-input rate text-right" data-validation="required">
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot">
                    <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                        <button type="submit" class="btn btn-primary btnSave">
                            Save
                        </button>
                        <button type="reset" class="btn btn-secondary btnCancel">
                            Cancel
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
			<div class="m-portlet m-portlet--mobile m-portlet--full-height">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Cut off period
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<form class="m-form m-form--fit" id="frmCutOffPeriod" method="POST" action="<?php echo site_url();?>eforms/billing/update_cutoffPeriod">
                    <div class="m-portlet__body">
                        <input type="hidden" name="id" value="id">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row m--margin-bottom-10">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-4 col-form-label">
                                        Day of * :
                                    </label>
                                    <div class="col-8">
                                        <input type="text" name="day" class="form-control m-input rate text-right" data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                            <button type="submit" class="btn btn-primary btnSave">
                                Save
                            </button>
                            <button type="reset" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
			<div class="m-portlet m-portlet--mobile m-portlet--full-height">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Due date
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						
					</div>
				</div>
				<form class="m-form m-form--fit" id="frmDueDate" method="POST" action="<?php echo site_url();?>eforms/billing/update_dueDate">
                    <div class="m-portlet__body">
                        <input type="hidden" name="id" value="id">
                        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="row m--margin-bottom-10">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-4 col-form-label">
                                        Number of days * :
                                    </label>
                                    <div class="col-8">
                                        <input type="text" name="day" class="form-control m-input rate text-right" data-validation="required">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                            <button type="submit" class="btn btn-primary btnSave">
                                Save
                            </button>
                            <button type="reset" class="btn btn-secondary btnCancel">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>