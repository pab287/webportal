<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Incentive <small>Payroll Type</small>
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <a class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air btnNew" href="javascript:void(0);" data-toggle="modal" data-target="#modal-add-incentive-type">
                                            <span>
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                        <br>
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-payroll-type--incentive" width="100%">
								<thead>
									<tr>
										<th>Code</th>
										<th>Description</th>
										<th>Date From</th>
										<th>Date To</th>
										<th>Status</th>
										<th>Action</th>
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
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-add-incentive-type">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form id="frm-add-incentive-type" method="post" action="<?php echo site_url("payroll/payroll_type/set_payroll_incentive_type"); ?>">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="modal-header">
					<h5 class="modal-title">Create Payroll Type Incentive</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Code *</label>
                        <input type="text" name="name" class="form-control m--uniqueCode" autocomplete="off" data-validation="required" />
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Description *</label>
                        <input type="text" name="description" class="form-control" autocomplete="off" data-validation="required" />
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">From Date *</label>
                                <div class='input-group date'>
                                    <input class="form-control m-input" 
                                        id="dt-picker_from"
                                        type="text" 
                                        name="date_from"
                                        autocomplete="off" 
                                        placeholder="MMM DD" 
                                        data-validation="required" />
                                    <span class="input-group-addon">
                                        <i class="la la-calendar glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">To Date *</label>
                                <div class='input-group date'>
                                    <input class="form-control m-input" 
                                        id="dt-picker_to"
                                        type="text" 
                                        name="date_to"
                                        autocomplete="off" 
                                        placeholder="MMM DD" 
                                        data-validation="required" />
                                    <span class="input-group-addon">
                                        <i class="la la-calendar glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-update-incentive-type">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="incentiveContainer">
            <form id="frm-update-incentive-type" method="post" action="<?php echo site_url("payroll/payroll_type/update_payroll_incentive_type"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                <input type="hidden" name="id" v-model="row.id" />
				<div class="modal-header">
					<h5 class="modal-title">Update Payroll Type Incentive</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label">Code *</label>
                        <input type="text" name="name" class="form-control m--uniqueCode" autocomplete="off" data-validation="required" v-model="row.name" />
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Description *</label>
                        <input type="text" name="description" class="form-control" autocomplete="off" data-validation="required" v-model="row.description" />
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">From Date *</label>
                                <div class='input-group date'>
                                    <input class="form-control m-input" 
                                        id="dt-picker_from"
                                        type="text" 
                                        name="date_from"
                                        autocomplete="off" 
                                        placeholder="MMM DD" 
                                        data-validation="required" v-model="row.dt_from" />
                                    <span class="input-group-addon">
                                        <i class="la la-calendar glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                            <div class="form-group">
                                <label class="form-control-label">To Date *</label>
                                <div class='input-group date'>
                                    <input class="form-control m-input" 
                                        id="dt-picker_to"
                                        type="text" 
                                        name="date_to"
                                        autocomplete="off" 
                                        placeholder="MMM DD" 
                                        data-validation="required" v-model="row.dt_to" />
                                    <span class="input-group-addon">
                                        <i class="la la-calendar glyphicon-th"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__group form-group">
                        <label for="">
                            Status *
                        </label>
                        <div class="m-checkbox-inline">
                            <label class="m-checkbox">
                                <input type="radio" name="is_active" value="1" v-model="row.is_active" />
                                Enabled
                                <span></span>
                            </label>
                            <label class="m-checkbox">
                                <input type="radio" name="is_active" value="0" v-model="row.is_active" />
                                Disabled
                                <span></span>
                            </label>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-archive-incentive-type">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" id="archiveIncentiveContainer">
            <form id="frm-archive-incentive-type" method="post" action="<?php echo site_url("payroll/payroll_type/archive_payroll_incentive_type"); ?>">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
                <input type="hidden" name="id" v-model="row.id" />
				<div class="modal-header">
					<h5 class="modal-title">Archive Payroll Type Incentive</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
                    <p>Are you sure you want to archive this entry <strong>`{{row.description}}`</strong> with payroll range from <strong>{{row.dt_from}} - {{row.dt_to}}</strong>?</p>;
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-submit btnArchive">YES</button>
                    <button type="button" class="btn btn-danger text-white btnCancel" data-dismiss="modal">NO</button>
				</div>
			</div>
		</div>
	</form>
</div>