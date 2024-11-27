<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Status Masterfile
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                            <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                            <span class="m-input-icon__icon m-input-icon__icon--left">
                                                <span>
                                                    <i class="la la-search"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="dropdown">
                                            <button class="btn btn-brand dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <a class="dropdown-item" href="#">
                                                    <i class="la la-search-plus"></i> Query Builder
                                                </a>
                                                <a class="dropdown-item" href="#">
                                                    <i class="la la-barcode"></i> Generate Barcode
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#">
                                                    <i class="la la-file-archive-o"></i> Mass Archive
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <button class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle='modal' data-target='#mdl_status_new'>
                                    <span>
                                        <i class="la la-plus"></i>
                                        <span>
                                            New
                                        </span>
                                    </span>
                                </button>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                    <!--end::Top function-->
                    <table class="table table-striped table-bordered" id="table-status" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>	
                        </tbody>
                    </table>
                    <!--end::Datatable-->
                </div>
                <!--end::Portlet body-->
			</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>

<div class="modal fade" id="mdl_status_new" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="frm_status_new" name="frm_status_new" action="<?php echo base_url("ams/assets/status_new");?>">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>" >
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Modal title
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group">
                        <label>
                            Name
                        </label>
                        <input type="text" name="name" class="form-control m-input" data-validation="required">
                    </div>
                    <div class="form-group m-form__group">
                        <label>
                            Location
                        </label>
                        <select id="select2_category" name="category" data-validation="required"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btnNew" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btnNew btn-submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>