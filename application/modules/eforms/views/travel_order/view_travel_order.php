<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #section-to-print, #section-to-print * {
            visibility: visible;
        }
        #section-to-print {
            position: absolute;
            left: 0;
            top: 0;
        }
    }

    @media screen and (max-width: 690px){
        #fix-mobile{
            flex-wrap: wrap;
        }

        #fix-mobile button, #fix-mobile a{
            flex: 1 1 22%;
            max-width: 100%;
        }

        #fix-mobile button, #fix-mobile a{
            margin-bottom: 10px;
        }
    }

    @media screen and (max-width: 480px){
        #fix-mobile button, #fix-mobile a{
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
<div class="m-content">
	  <div class="row">
        <div class="col-lg-12">
        <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="<?=isset($_GET['page']) && $_GET['page'] == 'archive' ? 'archive_travel_order' : 'masterfile' ?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                              Travel Order Details
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body">
                    <form id="travelDataRenderer">
                        <div class="row" >
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 ">
                                    Travel order No.
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="ref_no">
                                        <b v-text="vm_tab1.reference_no"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    File Under
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="ref_no">
                                        <b v-text="vm_tab1.company"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Type
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="ref_no">
                                        <b v-text="vm_tab1.type"></b>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row" >
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Created By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="edited">
                                        <b v-text="vm_tab1.created_by"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" >
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Last Edited By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="edited">
                                        <b v-text="vm_tab1.last_edited_by"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="recommend_by">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Recommended By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                                        <b v-text="vm_tab1.approved_recommend_by"></b>
                                    </div>   
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                       
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
                                        Remarks: <b v-text="vm_tab1.approved_recommend_remarks"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="approve">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Approved By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        <b v-text="vm_tab1.approved_by"></b>
                                    </div>
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        Remarks: <b v-text="vm_tab1.approved_remarks"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="disapprove">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Disapproved By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="disapprove">
                                        <b v-text="vm_tab1.disapproved_by"></b>
                                    </div>
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        Remarks: <b v-text="vm_tab1.disapproved_remarks"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="cancel">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Cancelled By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        <b v-text="vm_tab1.cancelled_by"></b>
                                    </div>
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        Remarks: <b v-text="vm_tab1.cancelled_remarks"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="noted">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Noted By
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="recieved">
                                        <b v-text="vm_tab1.hr_noted_by"></b>
                                    </div>   
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                       
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
                                        Remarks: <b v-text="vm_tab1.hr_noted_remarks"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="cancel_remark"></div>
                                <div class="form-group m-form__group row" id="noted_remark"></div>
                                <div class="form-group m-form__group row" id="accomplish_dt">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        <!-- Official Date Return --> Date Accomplished
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="recieved">
                                        <b v-text="vm_tab1.accomplished_by"></b>
                                        <!-- <b v-text="moment(vm_tab1.accomplishment_dt).format('LLL')"></b> -->
                                    </div>  
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                       
                                    </label>
                                    <!-- <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
                                        Remarks: <b v-text="vm_tab1.accomplishment_remarks"></b>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="m-separator m-separator--dashed d-xl-12"></div> 
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Official Station
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="ref_no">
                                        <b v-text="vm_tab1.station"></b>
                                    </div>
                                </div>
                                <!-- <div class="form-group m-form__group row">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                    Origin
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="ref_no">
                                        <b v-text="vm_tab1.origin"></b>
                                    </div>
                                </div>     -->
                                <div class="form-group m-form__group row">
                                    <label class="col-3">
                                    Status
                                    </label>
                                    <div class="col-9"  id="status">
                                    
                                    </div>
                                </div>  
                            </div> 
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group m-form__group row" id="vehicle">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Vehicle
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        <b v-text="vm_tab1.ref_yr"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="driver">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Driver
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        <b v-text="vm_tab1.driver"></b>
                                    </div>
                                </div>
                                <div class="form-group m-form__group row" id="other_remark">
                                    <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                                        Remarks
                                    </label>
                                    <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                                        <b v-text="vm_tab1.others_remarks"></b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="m-separator m-separator--dashed d-xl-12"></div> 
                    <br>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group m-form__group row">
                                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" id="table-personnel" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group m-form__group row">
                                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive-sm">
                                    <table class="table table-striped table-bordered" id="table-destination" width="100%">
                                        <thead>
                                            <tr>
                                                <!-- <th><input style="margin-left: 10px; transform: scale(1.3);" type="checkbox" id="checkAllBox"></th> -->
                                                <th>
                                                    <label class="m-checkbox m-checkbox--air m-checkbox--state-primary">
                                                        <input type="checkbox" id="checkAllBox"><span></span>
                                                    </label>
                                                </th>
                                                <th>Destination</th>
                                                <th>Date & time</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <br>
                    <div id="fix-mobile" class="modal-footer">
                        <button type="button" class="btn btn-info btnRecommend text-white" id="btnrecommentapprove" onclick="open_recommend_approve()">Recommend</button>
                        <button type="button" class="btn btn-success btnApprove_action text-white" id="btnapprove" onclick="open_approve()">Approve</button>
                        <button type="button" class="btn btn-success btnAccomplishment text-white" id="btnaccomplish" onclick="open_accomplish()">Accomplish</button>
                        <button type="button" class="btn btn-danger btnDisapprove_action text-white"  id="btndisapprove" onclick="open_disapprove()">Disapprove</button>
                        <button type="button" class="btn btn-warning btnEdit text-white" id="btnedit" onclick="edit_travel()">Edit</button>
                        <button type="button" class="btn btn-danger btnCancel text-white" id="btncancel" onclick="open_cancel()">Cancel</button>
                        <!-- <button type="button" class="btn btn-focus btnHr_note text-white" id="btnnote" onclick="open_note()">HR Note</button> -->
                        <button type="button" class="btn btn-danger btnUndo_recommend text-white" id="btnundoapproverecommend" onclick="undo_approve_recommend()">Undo Recommendation</button>
                        <button type="button" class="btn btn-danger btnUndo_approval text-white" id="btnundoapprove" onclick="undo_approve()">Undo Approval</button>
                        <button type="button" class="btn btn-danger btnUndo_accomplishment text-white" id="btnundoaccomplish" onclick="undo_accomplish()">Undo Accomplishment</button>
                        <button type="button" class="btn btn-danger btnUndo_disapproval text-white" id="btnundodisapprove" onclick="undo_disapprove()">Undo Disapproval</button>
                        <button type="button" class="btn btn-success btnRestore text-white" id="btnundocancel" onclick="restore()">Restore</button>
                        <button type="button" class="btn btn-danger btnUndo_hr_note text-white" id="btnundonote" onclick="undo_note()">Undo HR Note</button>
                        <button type="button" class="btn btn-primary btnPrint text-white" id="btnprint" onclick="printArea()">Print Form</button>
                        <button type="button" class="btn btn-primary btnPrint text-white" id="btnprint2" onclick="printArea2()">Print Trip Log</button>
                        <a href="<?php echo site_url("eforms/travel_order/masterfile"); ?>" class="btn btn-metal text-white m-btn--custom btnBack" id="btnback">Back</a>
                        <a href="<?php echo site_url("eforms/travel_order/archive_travel_order");?>" type="button" class="btn btn-metal btnArchive text-white  m-btn--custom " id="btnback2">Back</a>
                    </div>
                </div>
			</div>
        </div>
    </div>



    
</div>

<div class="modal fade" id="modal_recommend_approve" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-body form">
                <input type="hidden" value="" name="id"/>
                <div class="form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Remarks</label>
                    <div class="col-md-12">
                        <textarea name="approved_recommend_remarks"  class="form-control" rows="5" data-validation="required"></textarea> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">    
                <button type="submit" id="btnSave" onclick="approve_recommend()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon btnCancel" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_approve" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-body form">
                <input type="hidden" value="" name="id"/> 
                <div class="form-group">
                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Remarks</label>
                    <div class="col-md-12">
                        <textarea name="approved_remarks"  class="form-control" rows="5" data-validation="required"></textarea> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">    
                <button type="submit" id="btnSave" onclick="approve()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon btnCancel" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_disapprove" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form_disapprove" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body form">
                    <input type="hidden" value="" name="id"/> 
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12">Remarks</label>
                        <div class="col-md-12">
                            <textarea name="disapproved_remarks" rows="5" class="form-control" data-validation="required"> </textarea> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" onclick="disapprove()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">save</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnCancel" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_accomplish" role="dialog">
    <div class="modal-dialog" style="min-width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" style="font-weight: bold; color: #7e7e7e;"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form_accomplish" class="form-horizontal">
                <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="globalTempSelected" value="">
                <input type="hidden" name="param_id" value="">
                <div class="modal-body form">
                    <input type="hidden" value="" name="id"/> 
                    <div id="remarks" style="margin-bottom: 20px;">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12 required" style="font-weight: bold; color: #7e7e7e;">Unaccomplished Remarks</label>
                        <div class="col-md-12">
                            <textarea name="unacomplish_remarks" rows="5" class="form-control" data-validation="required"> </textarea> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-8 col-sm-8 col-xs-12 required" style="font-weight: bold; color: #7e7e7e;">Date Return</label>
                        <div class="col-12 input-group date" id="due_dt">
                            <input class="form-control m-input" type="text" name="accomplishment_dt" id="accomplishment_dt" maxlength="22" data-validation="required" readonly />
                            <span class="input-group-addon">
                                    <i class="la la-calendar glyphicon-th"></i>
                            </span>

                        </div>
                        <div class="col-12 mt-1">
                            <small class="m--font-danger"><i style="font-weight: bold;">Note:</i> Travel Orders cannot be accomplished if not completed within 15 days of the specified date and time.</small>
                        </div>
                    </div>
                    <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12 table-responsive-sm">
                        <table class="table table-striped table-bordered" id="table-selected-destination" width="100%">
                            <thead>
                                <tr>
                                    <th>Unaccomplished Destination</th>
                                    <th>Date & time</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon btnAccomplishment">Accomplish</button>
                    <button type="button" class="btn m-btn m-btn--custom m-btn--icon btn-metal text-white btnCancel" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_cancel" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>  
            </div>
            <form id="form_cancel" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body form">
                    <input type="hidden" value="" name="id"/>      
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12">Reason</label>
                        <div class="col-md-12">
                            <textarea name="cancelled_remarks"  class="form-control" rows="5" data-validation="required"> </textarea> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- <button type="submit" id="btnSave" onclick="cancel()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">save</button> -->
                    <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">save</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnCancel" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_noted" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form_noted" class="form-horizontal">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="modal-body form">
                    <input type="hidden" value="" name="id"/> 
                    <div class="form-group">
                        <label class="control-label col-md-2 col-sm-2 col-xs-12">Remarks</label>
                        <div class="col-md-12">
                            <textarea name="hr_noted_remarks"  class="form-control" rows="5" data-validation="required"> </textarea> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" onclick="noted()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                    <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnCancel" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--modal restore -->
<div class="modal fade" id="restore_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Restore Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "restore_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to restore this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnRestore">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="undo_approve_recommend_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Undo Recommendation Approval</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id = "undo_approve_recommend_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Undo recommendation approval of this form?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnUndo_approval">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>

<!--modal undo approve -->
<div class="modal fade" id="undo_approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Undo Approval</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
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
                    <button type="submit" class="btn btn-submit btn-primary btnUndo_approval">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>

<!--modal undo disapprove -->
<div class="modal fade" id="undo_disapprove_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Disapproval 
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
                    <button type="submit" class="btn btn-submit btn-primary btnUndo_disapproval">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnNew" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>

<!--modal undo hr not -->
<div class="modal fade" id="undo_hr_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Hr note
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_hr_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            are you sure you want to Undo hr note?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnUndo_hr_note">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>

<!--modal undo accomplishments -->
<div class="modal fade" id="undo_accomplishments_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Accomplishment
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_accomplishments_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            are you sure you want to Undo accomplishment?
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-submit btn-primary btnUndo_accomplishment">Yes</button>
                    <button type="button" class="btn btn-metal text-white btnCancel" data-dismiss="modal">No</button> 
                </div>
            </form>
        </div>
    </div>
</div>

<div class="m-content" id="testPrintable" hidden>
    <div class="m-portlet__body" id="printableArea">
        <div class="form-group m-form__group row" style="text-transform: uppercase;">
            <form id="formPrintArea">
            <div class="col-xs-12 table-responsive">
            <div class="row">   
            <table style="font-size:small;" width="100%" border="0" id="table_to_data">
                <tr>
                <td width="35%" style="font: 23px arial, sans-serif;"><p id="company" style="text-transform: uppercase;">{{vm_to_data.company}}</p></td>
                <td style="text-align: right;  font: 20px arial, sans-serif;"  width="30%"><p><small>TRAVEL ORDER</small><p></td>
                <td style="text-align: right; font: 23px arial, sans-serif;"  width="35%"><p id="reference_no">{{vm_to_data.reference_no}}</p></td>
                <td id="qr_code"></td>
                </tr>
            </table>
            </div>
            <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
            <div class="row">
            <div class="col-sm-12">
                <table style="font: 13px arial, sans-serif;" width="100%" border="0">
                <tr>
                    <td width="15%">Type</td>
                    <td width="40%"><div style="text-transform: uppercase;">: {{vm_to_data.type}}</div></td>
                    <td width="5%">Personnel</td>
                    <td width="30%"><div style="text-transform: uppercase;" v-for="vm_personnels in vm_personnel">: 
                        <b>{{ vm_personnels.employee_id}}</b><br>
                    </div></td>
                </tr>
                <tr>
                    <td width="15%">Duration</td>
                    <td width="50%">: {{ vm_to_data.duration }}</td>
                </tr>
                <tr>
                    <td width="15%">Official Station</td>
                    <td width="35%">: <b style="text-transform: uppercase;">{{ vm_to_data.station }}</b></td>
                </tr>
                <template>
                <tr>
                    <td width="15%">Vehicle</td>
                    <td width="35%">: <b id="vehicle_print" style="text-transform: uppercase;">{{ vm_to_data.plateno }}</b></td>
                </tr>
                </template>
                <tr>
                    <td width="15%"><span id="driver_label"></span></td>
                    <td width="35%"><b id="driver_print" style="text-transform: uppercase;">{{ vm_to_data.driver_print }}</b></td>          
                </tr>
                </table>
            </div>
            </div>
            <div class="row">
            <div class="col-6" style="width:100%;">
            
            </div>
            <div class="col-6" style="width:50%;">
                <table style="font: 13px arial, sans-serif;" width="100%" border="0">
                <tr>
                
                </tr>
                </table>
            </div>
            </div>
            <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
            <table style="font: 13px arial, sans-serif;" width="100%" border="0">
            <tr>
                <td style="text-align: left;" width="16%">Departure</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
            </tr>
            <tr>
                <td width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
            </tr>
            <tr>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:1px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
                <td style="border-top:0px dashed black;" width="5%">&nbsp;</td>
                <td style="border-top:0px dashed black;" style="text-align: left;" width="25%">&nbsp;</td>
            </tr>
            <tr >
                <td style="text-align: left;" width="16%">Guard on duty</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">Time&emsp;&emsp;&emsp;&emsp;</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">Approving Authority</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">Fuel Tender</td>
                <td width="5%">&nbsp;</td>
                <td style="text-align: left;" width="16%">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="9" width="100%">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="9" width="100%">&nbsp;</td>
            </tr>
            </table>
            <div class="row">
            <div class="col-xs-12 table-responsive">
                <table style="font: 13px arial, sans-serif;" class="table table-striped">
                <thead>
                    <tr>
                    <th style="text-align: left;" width="55%">Destination</th>
                    <th style="text-align: left;"width="40%">Date & Time</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <template v-for="vm_destinations in vm_destination">
                        <tr>
                            <td style="text-transform: uppercase;">
                                <b>{{vm_destinations.destination}}</b><br>
                                {{vm_destinations.purpose}}<br>
                                Requested by: {{vm_destinations.requested_by}}<br>
                                Remarks: {{vm_destinations.remarks}}<br>
                                Special Instruction: {{vm_destinations.instructions}}
                            </td>
                            <td>{{vm_destinations.date_from}} - {{vm_destinations.date_to}}</td>
                        </tr>
                    </template>
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </form>
        </div>
    </div>
</div>
<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableArea2">
    <div class="row">
          <div class="col-sm-12">
            <table style="font-size:small;" width="100%" border="0">
              <tr>
                <td style="text-align: center; font: 15px arial, sans-serif;"><b>TRIP LOG SHEET</h4></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table style="font: 12px arial, sans-serif;" width="100%" border="0">
              <tr>
                <td width="30%"><span>Vehicle:</span>&emsp;<u><b id="vehicle_print2"></b></u></td>
                <td width="30%"><span>Plate No:</span>&emsp;<u><b id="plate_no_print2"></b></u></td>
                <td width="40%">Date: _________________________</td>
              </tr>
              <tr>
                <td width="40%">Driver:&emsp;<u><b id="driver_print2"></b></u></td>
                <td width="16%">&nbsp;</td>
                <td width="34%">TO No:&emsp;<u><b id="ref_no_print"></b></u></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table id="table" style="border-right:1px solid black; border-collapse: collapse; font: 12px arial, sans-serif; margin-top:5px; margin-bottom:5px;" width="100%" border="0">
              <tr style="text-align: center;" >
                <td style="border-bottom:1px solid black; border-collapse: collapse;" width="3%">&nbsp;</td>
                <td style="border-bottom:1px solid black; border-collapse: collapse;" width="22%">&nbsp;</td>
                <td style="border-bottom:1px solid black; border-collapse: collapse;" width="14%">&nbsp;</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="14%" colspan="2" class="tb lb"><b>ODOMETER READING</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="24%" colspan="4" class="tb lb"><b>ACTUAL TIME / SIGNATURE(S)</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="5%" class="tb lb"><b>TONNAGE</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="8%" colspan="2" class="tb lb"><b>FUEL</b></td>
                <td style="border:1px solid black; border-collapse: collapse;" width="10%" class="tb lb rb"><b>REMARKS</b></td>
              </tr>
              <tr align="center" style="border:1px solid black; border-collapse: collapse;" class="tb bb">
                <td style="border:1px solid black; border-collapse: collapse;" width="3%" class="lb">No.</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="22%" class="lb">Itineraries</td>
                <td style="border:1px solid black; border-collapse: collapse;"width="14%" style="font-size:small;" class="lb">Date & Time</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="7%" class="lb">Departure</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="7%" class="lb">Arrival</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="6%" class="lb">Departure</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="6%" class="lb">Signature</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="6%" class="lb">Arrival</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="6%" class="lb">Signature</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="5%" class="lb">/m3</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="4%" class="lb">(LTR)</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="6%" class="lb">Signature</td>
                <td style="border:1px solid black; border-collapse: collapse;" width="8%" class="lb rb">&nbsp;</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-12">
            <table style="font: 13px arial, sans-serif; margin-top:35px;" width="100%" border="0">
              <tr>
                <td width="50%">Submitted By: ____________________________ </td>
                <td width="50%">Received By: ____________________________ </td>
              </tr>
            </table>
          </div>
        </div>
	</div>
</div>
    
