<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                SMS Protocol
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right">
                    <!---->	
                        <div class="row align-items-center">
                            <div class="col-xl-8 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <button id="add_new" data-toggle='modal' data-target='#new_modal' class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" stype="button">
                                            <span> 
                                                <i class="la la-plus"></i>
                                                <span>
                                                    New
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                                <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                    <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                                <div class="m-separator m-separator--dashed d-xl-none"></div>
                            </div>
                        </div>
                    </div>
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped table-bordered" id="table-sms-protocol" width="100%">
                            <thead>
                                <tr>
                                    <th>Server Name</th>
                                    <th>Port</th>
                                    <th>Username</th>
                                    <th>Department</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>	
                            </tbody>
                        </table>
                    </div>
				</div>
			</div>
			<!--end::Portlet-->
		</div>
	</div>
</div>

<div class="modal fade" id="new_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add SMS Protocol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="new_form" method="post" action="<?php echo site_url("configuration/set_sms_protocol_settings"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Server Name *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_ip" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Port *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_port" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Username *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_user" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Password *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_pass" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Department *
                        </label>
                        <div class="col-12">
                            <select id="select2_dprtmnt" name="department_id[]" class="form-control m-select2" multiple="multiple" data-validation="required"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>   
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit SMS Protocol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="edit_form" method="post" action="<?php echo site_url("configuration/update_sms_protocol_settings"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div  id="edit-modal_body" class="col-12 modal-body">
                    <input type="hidden" name="id" v-model="row.id" />
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Server Name *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_ip" data-validation="required" v-model="row.sms_ip" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Port *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_port" data-validation="required" v-model="row.sms_port" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Username *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_user" data-validation="required" v-model="row.sms_user" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Password *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="sms_pass" data-validation="required" v-model="row.sms_pass" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Department *
                        </label>
                        <div class="col-12">
                            <select id="select2_dprtmnt_edit" name="department_id[]" class="form-control m-select2" multiple="multiple" data-validation="required"></select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Remove Sms Protocol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="remove_form" method="post" action="<?php echo site_url("configuration/remove_sms_protocol_settings"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div  id="delete-modal_body" class="col-12 modal-body">
                    <input type="hidden" name="id" v-model="row.id" />
                    <p>Are you sure you want to remove this sms protocol?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnDelete">YES</button>
                    <button type="button" class="btn btn-danger text-white btnClose" data-dismiss="modal">NO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-connect" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            <input type="hidden" id="protocol_id">
            <input type="hidden" id="is_connected">
			<div class="modal-header">
				<h5 class="modal-title">Connect Protocol</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<i class="la la-info-circle"></i>  Are you sure you want to establish connection with this protocol? 
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btm-submit-connect btnConnect" onclick="toggle_connect()">Yes</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-exclude" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            <input type="hidden" id="exclude_protocol_id">
            <input type="hidden" id="exclude">
			<div class="modal-header">
				<h5 class="modal-title">Exclude Protocol</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<i class="la la-info-circle"></i>  Are you sure you want to establish connection with this protocol? 
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary btm-submit-connect btnConnect" onclick="toggle_exclude()">Yes</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
			</div>
		</div>
	</div>
</div>