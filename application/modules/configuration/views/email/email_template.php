<style>
table.dataTable td {
word-break: break-word;
}
</style>
<div class="m-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
                                Email Template
							</h3>
						</div>
					</div>
				</div>
				<div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
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
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">
                                <div class="col-xl-8 order-2 order-xl-1">
                                    <div class="colms-12">
                                        <div class="btn-group m-btn-group" role="group" aria-label="...">
                                            <button type="button" class="btn btn-primary" id="reload_dtTbl">
                                                <i class="la la-refresh"></i>
                                            </button>
                                            <div class="m-btn-group btn-group" role="group">
                                                <button id="tbl-btn-share" type="button" class="btn btn-success m-btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-table"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                    <a class="dropdown-item" href="#">
                                                        Dropdown link
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="m-btn-group btn-group" role="group">
                                                <button id="tbl-btn-share" type="button" class="btn btn-info m-btn m-btn--pill-last dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="la la-share"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 40px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <a class="dropdown-item" href="#">
                                                        Excel
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="m-datatable m-datatable--default m-datatable--scroll table-responsive-sm">
                        <table class="table table-striped table-bordered table-condensed table-small" id="table-email-template" width="100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Module</th>
                                    <th>Send To</th>
                                    <th>CC To</th>
                                    <th>BCC to</th>
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

<div class="modal fade" id="new_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Add Data
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "new_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Name:
                        </label>
                        <div class="col-12">
                            <input class="form-control" name="name" data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Description:
                        </label>
                        <div class="col-12">
                            <input class="form-control" name="description"  data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Module:
                        </label>
                        <div class="col-12">
                            <input class="form-control" name="module"  data-validation="required"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Send To:
                        </label>
                        <div class="col-12">
                            <select id="send_to" name="send_to[]"  data-validation="required">
                                
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Cc to:
                        </label>
                        <div class="col-12">
                            <select id="cc_to" name="cc_to[]">
                                
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            BCC to:
                        </label>
                        <div class="col-12">
                            <select id="bcc_to" name="bcc_to[]">
                                
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Save
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        Close
                    </button>   
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Edit Data
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="edit_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Name:
                        </label>
                        <div class="col-12">
                            <input class="form-control" name="edit_name" id="edit_name"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Description:
                        </label>
                        <div class="col-12">
                            <input class="form-control" name="edit_description" id="edit_description"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Module:
                        </label>
                        <div class="col-12">
                            <input class="form-control" name="edit_module" id="edit_module"/>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Send To:
                        </label>
                        <div class="col-12">
                            <select id="edit_send_to" name="edit_send_to[]"  data-validation="required">
                                
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            Cc to:
                        </label>
                        <div class="col-12">
                            <select id="edit_cc_to" name="edit_cc_to[]">
                                
                            </select>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-6 col-form-label form-control-label">
                            BCC to:
                        </label>
                        <div class="col-12">
                            <select id="edit_bcc_to" name="edit_bcc_to[]">
                                
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Save
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        Close
                    </button>   
                </div>
            </form>
        </div>
    </div>
</div>

<!--delete -->
<div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Delete Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "delete_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to remove this data?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>

<!--send email -->
<div class="modal fade" id="send_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Send Email
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "send_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Send test email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>