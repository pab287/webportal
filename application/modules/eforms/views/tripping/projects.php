<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Projects
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
										<a href="javascript:void();" data-toggle="modal" data-target="#m_newProject" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew text-white">
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
							<table class="table table-striped table-bordered" id="table-projects" width="100%">
								<thead>
									<tr>
										<th>Code</th>
										<th>Company</th>
										<th>Name</th>
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


<div class="modal fade" id="m_newProject" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					New Project
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmNewProject" action="<?php echo site_url('eforms/tripping/new_project');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
				<div class="modal-body">
					<div class="form-group m-form__group">
						<label for="companySelect">
							Select Company
						</label>
						<select class="form-control m-input m-input--square" name="company_id" id="companySelect" data-validation="required">
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Code
						</label>
						<input class="form-control m-input" type="text" name="code" data-validation="required">
					</div>
					<div class="form-group m-form__group">
						<label>
							Name
						</label>
						<input class="form-control m-input" type="text" name="name" data-validation="required">
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

<div class="modal fade" id="m_editProject" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">
					Edit Project
				</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						×
					</span>
				</button>
			</div>
			<form id="frmEditProject" action="<?php echo site_url('eforms/tripping/update_project');?>">
			<input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="id">
				<div class="modal-body">
					<div class="form-group m-form__group">
						<label for="companySelect">
							Select Company
						</label>
						<select class="form-control m-input m-input--square" name="company_id" id="companySelectU" data-validation="required">
						</select>
					</div>
					<div class="form-group m-form__group">
						<label>
							Code
						</label>
						<input class="form-control m-input" type="text" name="code" data-validation="required">
					</div>
					<div class="form-group m-form__group">
						<label>
							Name
						</label>

						<input class="form-control m-input" type="text" name="name" data-validation="required">
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