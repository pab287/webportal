<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="index" title="Go to Masterfile"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Allowance <span><small>(Archive)</small></span>
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
							<table class="table table-striped table-bordered" id="table-allowance" width="100%">
								<thead>
									<tr>
										<th>Code</th>
										<th>Name</th>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-restore">
	<form id="frm-restore">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Restore Allowance</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            <b>Restore</b> data to masterfile table. Click <b>yes</b> if you wish to proceed.
                        </label>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary btn-submit btnRestore">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button>
				</div>
			</div>
		</div>
	</form>
</div>
