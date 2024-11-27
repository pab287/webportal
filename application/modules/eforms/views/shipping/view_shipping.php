<style>
    .btn {
        margin-bottom: 3px;
        margin-right: 3px;
    }

    @media screen and (max-width: 690px){
        #buttons{
            display: flex;
            flex-wrap: wrap;
        }

        #buttons button, #buttons a{
            flex: 1 1 22%;
            max-width: 100%;
        }

        #buttons button, #buttons a{
            margin-bottom: 10px;
        }
    }

    @media screen and (max-width: 480px){
        #buttons button, #buttons a{
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
							<h3 class="m-portlet__head-text">
								Shipping Advice Form
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
				<div class="m-portlet__body">
                    <form action="#" id="form_shipping" class="form-horizontal">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div id="shipping_renderer">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group m-form__group row">
                                        <label class="col-4">
                                            Type
                                        </label>
                                        <div class="col-8">
                                            <b id="ship_type"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-4">
                                            Shipping Advice #:
                                        </label>
                                        <div class="col-8">
                                            <b v-text="vm_tab1.reference_no"></b>
                                        </div>
                                    </div>
                                    <br class="d-none d-md-block">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            File Under:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="company" >
                                            <b v-text="vm_tab1.company"></b><br>
                                            <span v-text="vm_tab1.department"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Priority:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.priority"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-4">
                                            Status:
                                        </label>
                                        <div class="col-8">
                                            <span id="status_color"><b v-text="vm_tab1.status"></b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Requested By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" >
                                            <b v-text="vm_tab1.display_name"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Created By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" >
                                            <b v-text="vm_tab1.created_by"></b><span> on </span><b v-text="moment(vm_tab1.created_dt).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Last Edited By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.last_edited_by"></b> <span id="last_edited_by"></span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="approve_by">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Approved By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.approved_by"></b><span> on </span><b v-text="moment(vm_tab1.approved_dt).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="disapproved_by">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Disapproved By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.disapproved_by"></b><span> on </span><b v-text="moment(vm_tab1.disapproved_dt).format('LLL')"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="receive_by">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Received By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" >
                                            <b v-text="vm_tab1.received_by"></b><span> on </span><b v-text="moment(vm_tab1.received_dt).format('LLL')"></b>
                                        </div>
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                           
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            Remarks: <b v-text="vm_tab1.received_remarks"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="cancelled">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Cancelled By:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" >
                                            <b v-text="vm_tab1.cancelled_by"></b><span> on </span><b v-text="moment(vm_tab1.cancelled_dt).format('LLL')"></b>
                                        </div>
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                           
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            Remarks: <b v-text="vm_tab1.cancelled_remarks"></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-separator m-separator--dashed d-xl-12"></div>
                            <br class="d-none d-md-block">
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group m-form__group row" id="shipto">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Ship To:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.ship_to_name"></b><br><br>
                                            <span id="internal_det">
                                                <span>Location: </span><b v-text="vm_tab1.location"></b><br>
                                                <span>Exact Address: </span><b v-text="vm_tab1.ship_to_address"></b>
                                            </span>
                                            <span id="external_det">
                                                <span>Company: </span><b v-text="vm_tab1.company_to"></b><br>
                                                <span>Department: </span><b v-text="vm_tab1.department_to"></b><br>
                                                <span>Exact Address: </span><b v-text="vm_tab1.ship_to_address"></b>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="disapproved_by">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Ship Date & Time:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="moment(vm_tab1.ship_date).format('LLL')"></b>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div id="service">
                                        <div class="form-group m-form__group row" id="reason">
                                            <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                                Plate No.:
                                            </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" >
                                                <b v-text="vm_tab1.plateno"></b>
                                            </div>
                                        </div>
                                        <div class="form-group m-form__group row" id="driver">
                                            <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                                Driver:
                                            </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12" id="created_by">
                                                <b v-text="vm_tab1.driver_name"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="others">
                                        <div class="form-group m-form__group row">
                                            <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                                Remarks:
                                            </label>
                                            <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                                <b v-text="vm_tab1.others_remarks"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Transporter:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.transporter"></b>
                                        </div>
                                    </div>
                                    <div class="form-group m-form__group row" id="waybill">
                                        <label class="col-md-4 col-lg-4 col-sm-4 col-xs-12">
                                            Courier & waybill #:
                                        </label>
                                        <div class="col-md-8 col-lg-8 col-sm-8 col-xs-12">
                                            <b v-text="vm_tab1.waybill"></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div>
                        <br class="d-none d-md-block">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-2 col-sm-2 col-xs-12 col-form-label">
                                        Contents
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-form__group row">
                                    <div class="m_datatable  m-datatable--default  m-datatable--scroll table-responsive col-12 table-responsive-sm">
                                        <table class="table table-striped table-bordered" id="table-shipping-content" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Asset/Stock Code</th>
                                                    <th>Quantity</th>
                                                    <th>Name</th>
                                                    <th>Purpose</th>
                                                </tr>
                                            </thead>
                                            <tbody> 

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" id="">
                            <div id="buttons"></div>
                       
                        </div>
                    </form>
                </div>
            </div>
	    </div>
    </div>    
</div>

<!--approve-->
<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Approve Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "approve_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to approve this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnSave">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnClose" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--undo approve-->
<div class="modal fade" id="undo_approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Approval Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_approve_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Undo approval of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--disapprove-->
<div class="modal fade" id="disapprove_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    disApprove Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "disapprove_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to disapprove this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--undo disapprove-->
<div class="modal fade" id="undo_disapprove_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    undo disApproval Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_disapprove_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Undo disapproval of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal cancel-->
<div class="modal fade" id="cancel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Cancel Shipping Advice Form
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "cancel_modal_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-md-2 col-sm-2 col-xs-12 text-right col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <textarea class="form-control" name="cancelled_remarks" rows="5"></textarea>
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
<!--undo cancel-->
<div class="modal fade" id="undo_cancel_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Undo Cancel
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_cancel_form">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Undo cancellation of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal receive-->
<div class="modal fade" id="receive_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Receive Shipping Advice
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="receieve_modal_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-md-2 col-sm-2 col-xs-12 text-right col-form-label form-control-label">
                            Remarks:
                        </label>
                        <div class="col-md-10 col-sm-10 col-xs-12">
                            <textarea class="form-control" rows="5" name="received_remarks"></textarea>
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
<!--undo receive-->
<div class="modal fade" id="undo_receive_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Undo Receiving Form
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id="undo_receive_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Undo receiving of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnNew">
                        Yes
                    </button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">
                        No
                    </button>				    
                </div>
            </form>
        </div>
    </div>
</div>


<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableArea">
        <form id="print_shipping" style="text-transform: uppercase;">
            <div class="row">
                <div class="col-md-12">
                    <table style="font-size:small;" width="100%" border="0">
                        <tr>
                            <td width="50%"><h1><div id="company_from" v-text="vm_tab1.company"></div></h1></td>
                            <td align="right" width="25%"><h2><small>SHIPPING ADVICE</small></h2></td>
                            <td align="right" width="25%"><h1><div v-text="vm_tab1.reference_no"></div></h1></td>
                        </tr>
                        <tr>
                            <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
                        </tr>
                    </table>
                    <hr style="border-top: 1px dashed black;">
                    <table width="100%" style="font-size:small" border="0">
                        <tr>
                            <td width="15%">Ship To</td>
                            <td width="35%">: <b v-text="vm_tab1.ship_to_name"></b></td>
                            <td width="15%"></td>
                            <td width="35%" rowspan="5" v-if="vm_tab1.cat === 'ex'"><img style="float:right;" v-bind:src="generateQR()" /></td>
                            <td width="35%" rowspan="4" v-else><img style="float:right;" v-bind:src="generateQR()" /></td>
                        </tr>
                        <tr class="internal">
                            <td width="15%" valign="top">Location</td>
                            <td width="35%" colspan="3">: <b v-text="vm_tab1.location"></b></td>
                        </tr>
                        <tr class="external">
                            <td width="15%" valign="top">Department</td>
                            <td width="35%" colspan="3">: <b v-text="vm_tab1.department_to"></b></td>
                        </tr>
                        <tr class="external">
                            <td width="15%" valign="top">Company</td>
                            <td width="35%" colspan="3">: <b v-text="vm_tab1.company_to"></b></td>
                        </tr>
                        <tr>
                            <td width="15%" valign="top">Exact Address</td>
                            <td width="35%" colspan="3">: <b v-text="vm_tab1.ship_to_address"></b></td>
                        </tr>
                        <tr>
                            <td width="15%">Date</td>
                            <td width="35%">: <b style="text-transform: uppercase;" v-text="moment(vm_tab1.ship_date).format('LLL')"></b></td>
                        </tr>
                        <tr>
                            <td width="15%">Plate No.</td>
                            <td width="35%" class="service">: <b v-text="vm_tab1.plateno"></b></td>
                            <td width="15%" class="others">Remarks</td>
                            <td width="35%" class="others">: <b v-text="vm_tab1.others_remarks"></b></td>
                            <td width="15%">Transporter&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td width="35%">: <b v-text="vm_tab1.transporter"></b></td>
                        </tr>
                        <tr>
                            <td width="20%">Driver</td>
                            <td width="30%">: <b id="driver" v-text="vm_tab1.driver_name"></b></td>
                            <td width="20%" class="external">Courier & waybill #</td>
                            <td width="30%" class="external">: <b v-text="vm_tab1.waybill"></b></td>
                        </tr>
                    </table>
                    <hr style="margin:10px 0px 5px 0px;border-top: 1px dashed black;">
                    <table  id="content" style="text-align: left; border-collapse: collapse;border: 0px solid black;"  width="100%">
                        <thead>
                            <tr style="border-bottom: 2px solid black;">
                                <th style="border-bottom: 2px solid black;">Asset/Stock Code</th>
                                <th style="border-bottom: 2px solid black;">Quantity</th>
                                <th style="border-bottom: 2px solid black;" align="center">Name</th>
                                <th style="border-bottom: 2px solid black;" align="center">Purpose</th>
                            </tr>
                        </thead>
                        <tbody> 
                            <tr v-for="vm_contents_print in vm_content">
                                <td align="left">{{ vm_contents_print.stock_code }}</td>
                                <td align="left">{{ vm_contents_print.quantity}} {{vm_contents_print.uom }}</td>
                                <td align="center">{{ vm_contents_print.description }}</td>
                                <td align="center">{{ vm_contents_print.item_purpose }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table  style="font-size:small">

                    </table>
                    <hr style="margin:0px;border-top: 2px solid black;">
                        <table style="font-size:small;" width="100%" border="0">
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;<b id="approver" v-text="vm_tab1.approved_by"></b></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr style="border-bottom: 1px dashed black;"></td>
                                <td colspan="2"><hr style="border-bottom: 1px dashed black;"></td>
                            </tr>
                            <tr>
                                <td width="35%">Approving Authority</td>
                                <td width="15%" align="right">Time</td>
                                <td width="35%"><div style="margin-left: 5px">Guard on duty</div></td>
                                <td width="15%" align="right">Time</td>
                            </tr>
                        </table>
                        <table style="font-size:small;" width="100%" border="0">
                            <tr>
                                <td width="35%">&nbsp;</td>
                                <td width="15%">&nbsp;</td>
                                <td width="35%">&nbsp;</td>
                                <td width="15%">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td style="border-top: 1px dashed black;"><div style="margin-left: 5px">Received By(Printed Name and Signature)</div></td>
                                <td align="right" style="border-top: 1px dashed black;">Time</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>