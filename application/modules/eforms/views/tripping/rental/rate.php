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
										<th>Unit</th>
										<th>Rate</th>
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
					New Rental Rate
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmNewRate" action="<?php echo site_url('eforms/tripping/new_rental_rate');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="modal-body">
                    <div class="form-group m-form__group m--margin-top-10">
                        <div class="alert m-alert m-alert--default" role="alert">
                            Allowances are calculated per hour.
                        </div>
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
							Rate
						</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input class="form-control m-input" type="text" name="rate" data-validation="required">
                            </div>
                            <div class="col-md-4">
                            per hour
                            </div>
                        </div>
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
					Edit Rental Rate
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmEditRate" action="<?php echo site_url('eforms/tripping/update_rental_rate');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="id">
				<div class="modal-body">
                    <div class="form-group m-form__group m--margin-top-10">
                        <div class="alert m-alert m-alert--default" role="alert">
                            Allowances are calculated per hour.
                        </div>
                    </div>
                    <div class="form-group m-form__group">
						<label for="driversUnitSelect">
							Select Vehicle/Unit
						</label>
						<select class="form-control m-input m-input--square" id="driversUnitSelectU" name="unit" data-validation="required">
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Rate
						</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input class="form-control m-input" type="text" name="rate" data-validation="required">
                            </div>
                            <div class="col-md-4">
                            per hour
                            </div>
                        </div>
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