<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Telegram Protocol
							</h3>
						</div>
					</div>
                    <div class="m-portlet__head-tools">
                        <a  href="<?php echo site_url("configuration/telegram_protocol_archive");?>" class="btnView">
                            <i class="m-nav__link-icon flaticon-open-box"></i>
                            <span class="m-nav__link-text">
                                Archives
                            </span>
                        </a>
                    </div>
				</div>
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right">
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
                        <table class="table table-striped table-bordered" id="table-telegram-protocol" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Module</th>
                                    <th>Bot Chat Id</th>
                                    <th>Token</th>
                                    <th>Created At</th>
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


<div class="modal fade" id="new_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Telegram Bot Protocol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="new_form" method="post" action="<?php echo site_url("configuration/set_telegram_protocol_settings"); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Bot Name *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="bot_name" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Bot Description *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="bot_description" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Module *
                        </label>
                        <div class="col-12">
                            <select id="select2_module" name="modules[]" class="form-control m-select2" data-validation="required" multiple>
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Bot Owner *
                        </label>
                        <div class="col-12">
                            <select id="select2_owner" name="owner_id" class="form-control m-select2" data-validation="required">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Chat Id *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="chat_id" data-validation="required" autocomplete="off" />
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Telegram Bot Token *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="telegram_bot_token" data-validation="required" autocomplete="off" />
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
                <h5 class="modal-title" id="exampleModalLabel">Edit Telegram Bot Protocol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="edit_form_telegram">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div  id="edit-modal_body" class="col-12 modal-body">
                    <input type="hidden" name="id" v-model="row.id" />
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Bot Name *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="bot_name" data-validation="required" autocomplete="off" v-model="row.bot_name"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Bot Description *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="bot_description" data-validation="required" autocomplete="off" v-model="row.bot_description"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Module *
                        </label>
                        <div class="col-12">
                            <select id="select2_module_edit" name="modules[]" class="form-control m-select2" data-validation="required" multiple  v-model="row.modules">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Bot Owner *
                        </label>
                        <div class="col-12">
                            <select id="select2_owner_edit" name="owner_id" class="form-control m-select2" data-validation="required" v-model="row.owner_id">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Chat Id *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="chat_id" data-validation="required" autocomplete="off" v-model="row.chat_id"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Telegram Bot Token *
                        </label>
                        <div class="col-12">
                            <input type="text" class="form-control" name="telegram_bot_token" data-validation="required" autocomplete="off" v-model="row.telegram_bot_token"/>
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