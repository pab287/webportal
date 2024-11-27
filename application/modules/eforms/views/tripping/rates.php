<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Rates
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
                </div>
                <div class="m-portlet__body">

					<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
						<div class="row align-items-center">
							<div class="col-xl-8 order-2 order-xl-1">
								<div class="form-group m-form__group row align-items-center">
									<div class="col-md-4">
										<a href="javascript:void();" data-toggle="modal" data-target="#m_newRate" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
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
						</div>
						<div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">	
							<div class="row align-items-center">
								<div class="col-xl-8 order-2 order-xl-1">
									
								</div>
							</div>
						</div>

						<!--begin: Datatable -->
						<div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
							<table class="table table-striped table-bordered" id="table-rates" width="100%">
								<thead>
									<tr>
										<th>Departure</th>
										<th>Destination</th>
										<th>Rate</th>
										<th>Project</th>
										<th>Driver Type</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>	
								</tbody>
							</table>
						</div>
					<!--end: Datatable -->
					</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="m_newRate" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					New Rate
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmNewRate" action="<?php echo site_url('eforms/tripping/new_rate');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="modal-body">
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Project
						</label>
						<select class="form-control m-input m-input--square" name="project_id" id="projectSelect" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Departure
						</label>
						<select class="form-control m-input m-input--square" name="from" id="departureSelect" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Destination
						</label>
						<select class="form-control m-input m-input--square" name="to" id="destinationSelect" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="typeSelect">
							Driver Type
						</label>
						<select class="form-control m-input m-input--square" id="typeSelect" name="driver_type" data-validation="required">
						<option value="old">Old</option>
						<option value="new">New</option>
						<option value="na">Not Applicable</option>
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="typeSelect">
							Period
						</label>
						<select class="form-control m-input m-input--square" id="typeSelect" name="period" data-validation="required">
						<option value="old">AM</option>
						<option value="new">PM</option>
						<option value="na">Not Applicable</option>
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Rate
						</label>
						<input class="form-control m-input" type="text" name="rate" data-validation="required">
					</div>
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
						Close
					</button>
					<button type="submit" class="btn btn-primary btnSave" >
						Save
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="m_editRate" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					Edit Rate
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmEditRate" action="<?php echo site_url('eforms/tripping/update_rate');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="id">
				<div class="modal-body">
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Project
						</label>
						<select class="form-control m-input m-input--square" name="project_id" id="projectSelectU" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Departure
						</label>
						<select class="form-control m-input m-input--square" name="from" id="departureSelectU" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="driversSelect">
							Destination
						</label>
						<select class="form-control m-input m-input--square" name="to" id="destinationSelectU" data-validation="required">
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="typeSelect">
							Driver Type
						</label>
						<select class="form-control m-input m-input--square" id="typeSelect" name="driver_type" data-validation="required">
						<option value="na">Not Applicable</option>
						<option value="old">Old</option>
						<option value="new">New</option>
						</select>
					</div>
                    <div class="form-group m-form__group">
						<label for="typeSelect">
							Period
						</label>
						<select class="form-control m-input m-input--square" id="typeSelect" name="period" data-validation="required">
						<option value="na">Not Applicable</option>
						<option value="old">AM</option>
						<option value="new">PM</option>
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Rate
						</label>
						<input class="form-control m-input" type="text" name="rate" data-validation="required">
					</div>
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
						Close
					</button>
					<button type="submit" class="btn btn-primary btnUpdate" >
						Update
					</button>
				</div>
			</form>
		</div>
	</div>
</div>