<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Drivers
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
										<a href="javascript:void();" data-toggle="modal" data-target="#m_newDriver" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
											<span>
												<i class="la la-plus"></i>
												<span>
													New
												</span>
											</span>
										</a>
										<button class="btn btn-success m-btn m-btn--icon m-btn--pill btnBilling_settings" type="button">
											Settings
										</button>
										<button class="btn btn-primary m-btn m-btn--icon m-btn--pill btnAdvance_search" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<span><i class="fa fa-search"></i><span>Filter</span><span class="dropdown-toggle"></span></span>
										</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
											<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-query-builder">
												Query Builder
											</a>
										</div>
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
							<table class="table table-striped table-bordered" id="table-drivers" width="100%">
								<thead>
									<tr>
										<th>Code</th>
										<th>Name</th>
										<th>Unit/Truck</th>
										<th>Type</th>
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


<div class="modal fade" id="m_newDriver" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					New Driver
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmNewDriver" action="<?php echo site_url('eforms/tripping/new_driver');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="modal-body">
					<div class="form-group m-form__group">
						<label for="driversSelect">
							Select Driver
						</label>
						<select class="form-control m-input m-input--square" name="emp_id" id="driversSelect" data-validation="required">
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Code
						</label>

						<input class="form-control m-input" type="text" name="code" data-validation="required">
					</div>
					<div class="form-group m-form__group">
						<label for="typeSelect">
							Select Type
						</label>
						<select class="form-control m-input m-input--square" id="typeSelect" name="type" data-validation="required">
						<option value="old">Old</option>
						<option value="new">New</option>
						</select>
					</div>
					<div class="form-group m-form__group">
						<label for="driversUnitSelect">
							Select Vehicle/Unit
						</label>
						<select class="form-control m-input m-input--square" id="driversUnitSelect" name="vehicle_id">
						</select>
					</div>
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
						Close
					</button>
					<button type="submit" class="btn btn-primary btnUpdate" >
						Save
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="m_editDriver" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					Edit Driver
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmEditDriver" action="<?php echo site_url('eforms/tripping/update_driver');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="id">
				<div class="modal-body">
					<div class="form-group m-form__group">
						<label for="driversSelect">
							Select Driver
						</label>
						<select class="form-control m-input m-input--square" name="emp_id" id="driversSelectU" data-validation="required">
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Code
						</label>

						<input class="form-control m-input" type="text" name="code" data-validation="required">
					</div>
					<div class="form-group m-form__group">
						<label for="typeSelect">
							Select Type
						</label>
						<select class="form-control m-input m-input--square" id="typeSelectU" name="type" data-validation="required">
						<option value="old">Old</option>
						<option value="new">New</option>
						</select>
					</div>
					<div class="form-group m-form__group">
						<label for="driversUnitSelect">
							Select Vehicle/Unit
						</label>
						<select class="form-control m-input m-input--square" id="driversUnitSelect" name="vehicle_id" >
						</select>
					</div>
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-danger btnClose" data-dismiss="modal">
						Close
					</button>
					<button type="submit" class="btn btn-primary btnSave" >
						Update
					</button>
				</div>
			</form>
		</div>
	</div>
</div>