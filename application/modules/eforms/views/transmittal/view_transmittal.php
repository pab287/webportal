<style scoped>
  @media print{
    
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
                  <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
							<h3 class="m-portlet__head-text">
								View Transmittal
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body" >
          <form id="transmittalDataRenderer"> 
					<div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 ">
                  Transmittal No.
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="ref_no"><b v-text="vm_tab1.reference_no"></b></div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Document Source
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="source">
                 <b v-text="vm_tab1.company_from"></b>
                 <p v-text="vm_tab1.department_from"></p>
                </div>
              </div>
              <br class="d-none d-md-block">
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Status
                </label>
                <div class="col-9"  id="status">
                  
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Priority
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="priority">
                  <b v-text="vm_tab1.priority"></b>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 ">
                  From
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="from">
                  <b v-text="vm_tab1.created_by"></b>
                </div>
              </div>
             
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Other Information
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="info">
                  <b  class="m--margin-top-10" v-text="vm_tab1.purpose"></b>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row" >
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Last Edited By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="edited">
                  <b id="last_edited_by"></b>
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
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
                  Remarks: <b v-text="vm_tab1.approve_remarks"></b>
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
                 <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
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
              </div>
              <div class="form-group m-form__group row" id="receive">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Received By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="recieved">
                  <b v-text="vm_tab1.received_by"></b>
                </div>
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
                Remarks: <b v-text="vm_tab1.received_remarks"></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="cancel_remark">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Reason
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b v-text="vm_tab1.cancelled_remarks"></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="receive_remark">
              
             
              </div>
            </div>
          </div>
          <br class="d-none d-md-block">
            <div class="m-separator m-separator--dashed d-xl-12"></div>  
          <br class="d-none d-md-block">
          <div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Requested By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="requested_by">
                  <b v-text="vm_tab1.requested_by"></b>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Deliver To
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="deliver_to">
                  <b v-text="vm_tab1.ship_to"></b>
                </div>
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="deliver_to">
                  <b v-text="vm_tab1.company"></b><br>
                  <b v-text="vm_tab1.department"></b><br>
                  <b v-text="vm_tab1.ship_to_address"></b><br>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Deliver Date & Time
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="deliver_date">
                  <b v-text="vm_tab1.ship_date"></b>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row" id="plate_no">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Service vehicle
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b v-text="vm_tab1.ref_yr"></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="driver">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Driver
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="driver">
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
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Transporter
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="transporter">
                  <b v-text="vm_tab1.transporter"></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="waybill">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Courier & Waybill #
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b v-text="vm_tab1.waybill"></b>
                </div>
              </div>
            </div>
          </div>
          <br class="d-none d-md-block">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group m-form__group row">
                <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12">
                  <table class="table table-striped table-bordered" id="table-content" width="100%">
                    <thead>
                      <tr>
                        <th>Content</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="vm_tab1_body_detail in vm_tab1_body">
                        <td style="word-break: break-word;" >{{ vm_tab1_body_detail.description }}</td>
                      </tr>
                      
                    </tbody>
                  </table>
                </div> 
              </div>
            </div>
          </div>
          </form>
          <br class="d-none d-md-block">
          <?php $this->current_action = $this->core_layout->getCurrentActions();?>
          <div id="fix-mobile" class="modal-footer">
            <?php if((in_array("approve_action", $this->current_action))): ?>
            <button class="btn btn-success btnApprove_action text-white" id="btnapprove" onclick="open_approve()">
              Approve
            </button>
            <?php endif; ?>
            <?php if((in_array("disapprove_action", $this->current_action))): ?>
            <button class="btn btn-danger btnDisapprove_action text-white"  id="btndisapprove" onclick="open_disapprove()">
              Disapprove
            </button>
            <?php endif; ?>
            <?php if((in_array("edit", $this->current_action))): ?>
            <button class="btn btn-warning btnEdit text-white" id="btnedit" style="color: #FFFFFF;" onclick="edit_transmittal()">
              Edit
            </button>
            <?php endif; ?>
            <?php if((in_array("cancel", $this->current_action))): ?>
            <button class="btn btn-danger btnCancel text-white" id="btncancel" onclick="open_cancel()">
              Cancel
            </button>
            <?php endif; ?>
            <?php if((in_array("receive", $this->current_action))): ?>
            <button class="btn btn-focus btnReceive text-white" id="btnreceive" onclick="open_receive()">
              Receive
            </button>
            <?php endif; ?>
            <?php if((in_array("undo_approval", $this->current_action))): ?>
            <button class="btn btn-danger btnUndo_approval text-white" id="btnundoapprove" onclick="openundo_approve()">
              Undo Approval
            </button>
            <?php endif; ?>
            <?php if((in_array("undo_disapproval", $this->current_action))): ?>
            <button class="btn btn-danger btnUndo_disapproval text-white" id="btnundodisapprove" onclick="open_undodisapprove()">
              Undo Disapproval
            </button>
            <?php endif; ?>
            <?php if((in_array("restore", $this->current_action))): ?>
            <button class="btn btn-success btnRestore text-white" id="btnundocancel" onclick="restore()">
              Restore
            </button>
            <?php endif; ?>
            <?php if((in_array("undo_receive", $this->current_action))): ?>
            <button class="btn btn-danger btnUndo_receive text-white" id="btnundoreceive" onclick="open_undoreceive()">
              Undo Receipt
            </button>
            <?php endif; ?>
            <?php if((in_array("print", $this->current_action))): ?>
            <button class="btn btn-primary btnPrint text-white" id="btnprint" onclick="printArea()">
                Print
            </button>
            <?php endif; ?>
            <?php if((in_array("back", $this->current_action))): ?>
              <a href="<?php echo site_url("eforms/transmittal/masterfile");?>" id="btnback" class="btn btn-metal btnBack text-white">
                <span>
                    BACK
                </span>
              </a>
              <a href="<?php echo site_url("eforms/transmittal/archive_transmittal");?>" id="btnback2" class="btn btn-metal btnBack text-white">
                <span>
                    BACK
                </span>
              </a>
            <?php endif; ?>
      </div>
				</div>
			</div>
			<!--end::Portlet-->
        <div class="modal fade" id="modal_form_approve" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title"></h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  
                </div>
                <div class="modal-body form">
                  <form action="#" id="form_approve" class="form-horizontal">
                    <input type="hidden" value="" name="id"/> 
                   <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                    <label class="control-label col-md-2 col-lg-2 col-sm-2 col-xs-12">Remarks</label>
                    <div class="col-md-12">
                     <!-- <textarea name="approve_remarks"  class="form-control" data-validation="required"> </textarea>  -->
                     <textarea name="approve_remarks"  class="form-control"> </textarea> 
                    </div>
                    </div>
                    
                    </div>
                   <div class="modal-footer">
                      
                     <!-- <button type="submit" id="btnSave" onclick="approve()" class="btn btn-primary m-btn m-btn--custom m-btn--icon btnSave">Save</button> // original source code --> 
                     <button type="button" id="btnSave" onclick="approve()" class="btn btn-primary m-btn m-btn--custom m-btn--icon btnSave">Save</button>
                      <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon btnClose" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                  </div><!-- /.modal-content -->
                </div>
    </div>
  </div>
   <div class="modal fade" id="modal_form_cancel" role="dialog">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title"></h3>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  
                </div>
                <div class="modal-body form">
                  <form action="#" id="form_cancel" class="form-horizontal">
                    <input type="hidden" value="" name="id"/> 
                   <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-group">
                    <label class="control-label col-md-2 col-lg-2 col-sm-2 col-xs-12">Reason</label>
                    <div class="col-md-12">
                     <!-- <textarea name="cancelled_remarks"  class="form-control" data-validation="required"> </textarea>  -->
                     <textarea name="cancelled_remarks"  class="form-control"> </textarea> 
                    </div>
                    </div>
                    
                    </div>
                   <div class="modal-footer">
                      
                     <button type="submit" id="btnSave" onclick="cancel()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
                      <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                  </div><!-- /.modal-content -->
                </div>
    </div>
  </div>

   <div class="modal fade" id="modal_form_received" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          
        </div>
        <div class="modal-body form">
          <form action="#" id="form_received" class="form-horizontal">
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="form-group">
            <label class="control-label col-md-2 col-lg-2 col-sm-2 col-xs-12">Remarks</label>
            <div class="col-md-12">
              <textarea name="received_remarks"  class="form-control" data-validation="required"> </textarea> 
            </div>
            </div>
            
            </div>
            <div class="modal-footer">
              
              <!-- <button type="submit" id="btnSave" onclick="receive()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button> // original source code -->
              <button type="button" id="btnSave" onclick="receive()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
              <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
            </div>
            </form>
          </div><!-- /.modal-content -->
        </div>
    </div>
  </div>

  <div class="modal fade" id="modal_form_undoreceived" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body form">
          <form action="#" id="form_received" class="form-horizontal">
            <div class="form-group m-form__group row">
                <label class="col-12 col-form-label form-control-label">
                    Undo receival of this form?
                </label>
            </div>
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">            
        </div>
        <div class="modal-footer">
            <!-- <button type="submit" id="btnSave" onclick="undo_receive()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">yes</button> // original source code -->
            <button type="button" id="btnSave" onclick="undo_receive()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">no</button>
            </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>


  <div class="modal fade" id="modal_form_disapprove" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form id="form_disapproved">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>"> 
        <div class="modal-body form">  
          <div class="form-group">
            <label class="control-label col-md-2 col-lg-2 col-sm-2 col-xs-12">Remarks</label>
            <div class="col-md-12">
              <!-- <textarea name="disapproved_remarks"  class="form-control" data-validation="required"> </textarea>  -->
              <textarea name="disapproved_remarks"  class="form-control"> </textarea> 
            </div>
          </div>
                     
        </div>
        <div class="modal-footer">
            <button type="button" id="btnSave" onclick="disapprove()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>

  <div class="modal fade" id="modal_form_undodisapprove" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body form">
              <div class="form-group m-form__group row">
                <label class="col-12 col-form-label form-control-label">
                    Undo disapproval of this form?
                </label>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">            
        </div>
        <div class="modal-footer">
            <button type="submit" id="btnSave" onclick="undo_disapprove()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div>
  </div>

  <div class="modal fade" id="modal_form_undoapprove" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group m-form__group row">
              <label class="col-12 col-form-label form-control-label">
                  Undo approval of this form?
              </label>
            </div>            
        </div>
        <div class="modal-footer">
            <button type="submit" id="btnSave" onclick="undo_approve()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">no</button>
        </div>
      </div><!-- /.modal-content -->
    </div>
  </div>

  </div>
</div>

<div class="modal fade" id="restore" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body form">
          <form action="#" id="restore_form" class="form-horizontal">
          <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">   
            <div class="form-group m-form__group row">
                <label class="col-12 col-form-label form-control-label">
                     Restore this form?
                </label>
            </div>
                     
        </div>
        <div class="modal-footer">
            <button type="submit" id="btnSave" onclick="restore_form()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">no</button>
            </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>

<div class="m-content" hidden>
<link href="<?php echo base_url("assets/fonts/montserrat/montserrat.css"); ?>" rel="stylesheet" type="text/css" />
	<div class="m-portlet__body" id="printableArea" style="font-family: 'Montserrat', sans-serif; text-transform: uppercase !important;">
    <div class="row">
      <div class="col-xs-12 table-responsive">
        <table style="font-size:small;" width="100%" border="0">
          <tr>
            <td width="50%"><h1><div id="company_from">{{ vmDataMain.company_from }}</div></h1></td>
            <td align="right" width="30%" style="margin-right: 0px;"><h3>TRANSMITTAL</h3></td>
            <th align="right" width="20%" style="margin-left: 0px; padding-left:0px;"><h1><div style="font-size: 20px;" id="refernce_no">{{ vmDataMain.reference_no }}</div></h1></th>
          </tr>
          <tr>
            <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
          </tr>
        </table>
        <hr style="margin:2px 0px 2px 0px;border-top: 1px dashed black;">
        <table style="font-size:small" width="100%">
          <tr>
            <td width="15%">Deliver To</td>
            <td colspan="3" width="85%">:<b id="ship_to_print"> {{ vmDataMain.ship_to }}</b></td>
          </tr>
          <tr id="company_print">
            <td width="15%"></td>
            <td colspan="3" width="85%">&nbsp<b id="company_to_print"> {{ vmDataMain.company }}</b></td>
          </tr>
          <tr id="department_print">
            <td width="15%"></td>
            <td colspan="3" width="85%">&nbsp<b id="department_to_print"> {{ vmDataMain.department }}</b></td>
          </tr>
          <tr>
            <td width="15%"></td>
            <td colspan="3" width="85%">&nbsp<b id="ship_to_address_print"> {{ vmDataMain.ship_to_address }}</b></td>
          </tr>
          <tr>
            <td width="15%">Date</td>
            <td width="35%">:<b id="ship_date_print"> {{ vmDataMain.ship_date }}</b></td>
          </tr>
          <tr>
            <td width="15%"><span id="vehicle_label"> {{ vmDataMain.vehicle_label }}</span></td>
            <td width="35%"><b id="vehicle_print"></b></td>
            <td width="20%">Transporter</td>
            <td width="35%">: <b id="transporter_print">{{ vmDataMain.transporter }}</b></td>
          </tr>
          <tr>
            <td width="15%"><span id="driver_label">Driver</span></td>
            <td width="35%"><b id="driver_print">: {{vmDataMain.driver}}</b></td>
            <td width="20%"><span class="waybill_print">Courier & waybill #</span></td>
            <td width="35%"><b id="waybill_print" class="waybill_print">{{vmDataMain.waybill}}</b></td>
            <!-- <td><div id="driver_label"></div></td>
            <td>: <b id="driver"></b></td> -->
          </tr>
        </table>
        <hr style="margin:5px 0px 10px 0px;border-top: 1px dashed black;">
        <table style="font-size:small;" width="100%">
          <tr><b>Content</b></tr>
        	<tr v-for="vm_transmittal in vmData">
            <td style="word-break: break-word;">&#x2022; {{ vm_transmittal.description }}</td>
          </tr>
       	</table>
        <hr style="margin:0px;border-top: 1px solid black;">
        <table style="font-size:small; width: 100%">
        	<tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
            	<td colspan="2"><div class="approvername">{{ vmDataMain.approved_name }}</div><hr style="margin:5px 5px 0px 0px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:5px 0px 0px 5px;border-top: 1px dashed black;"></td>
            </tr>
            <tr>
            	<td width="35%">Approving Authority</td>
                <td width="15%" align="right">Time &nbsp;</td>
                <td width="35%"><div style="margin-left: 5px">Guard on duty</div></td>
                <td width="15%" align="right">Time</td>
            </tr>
        </table>
        <table style="font-size:small; width: 100%">
        	<tr>
            	<td width="35%"></td>
                <td width="15%"></td>
                <td width="35%"></td>
                <td width="15%"></td>
            </tr>
        	<tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><div style="margin-left: 5px">Received By</div></td>
                <td align="right">Time</td>
            </tr>
        </table>
      </div>
      <!-- /.col -->
    </div>

	</div>
</div>
    
