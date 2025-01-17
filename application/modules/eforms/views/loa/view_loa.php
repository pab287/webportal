<style>
.btn{
  margin-left: 2.5px;
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
      <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
          <div class="m-portlet__head-caption">
            <div class="m-portlet__head-title">
              <span class="m-portlet__head-icon">
                <a type="button" href="<?=isset($_GET['page']) && $_GET['page'] === 'archive' ? 'archive_loa' : 'masterfile' ?>" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnNew">
                  <i class="la la-arrow-left"></i>
                </a>
              </span>
              <h3 class="m-portlet__head-text">
                View Leave of Absence
              </h3>
            </div>
          </div>
          <div class="m-portlet__head-tools"></div>
        </div>
        <div class="m-portlet__body" id="loaDataRenderer">
          <div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Reference No. </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                  <b v-text="vm_tab1.reference_no"></b>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3"> Status </label>
                <div class="col-9" id="status"></div>
              </div>
            </div>
          <div class="col-md-6 col-sm-12">
            <div class="form-group m-form__group row">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">Created By</label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.created_by"></b>
              </div>
            </div>
            <div class="form-group m-form__group row" v-if="vm_tab1.last_edited_by && vm_tab1.last_edited_by != ' '">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Last Edited By </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.last_edited_by"></b>
              </div>
            </div>
            <div class="form-group m-form__group row" id="approve">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Approved By </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.approved_by"></b>
              </div>
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"></label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" v-if="vm_tab1.approved_remarks && vm_tab1.approved_remarks != ' '">
                Remarks: <b v-text="vm_tab1.approved_remarks"></b>
              </div>
            </div>
            <div class="form-group m-form__group row" id="disapprove">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Disapproved By </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.disapproved_by"></b>
              </div>
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"></label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" v-if="vm_tab1.disapproved_remarks && vm_tab1.disapproved_remarks != ' '">
                Remarks: <b v-text="vm_tab1.disapproved_remarks"></b>
              </div>
            </div>
            <div class="form-group m-form__group row" id="cancel">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Cancelled By </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.cancelled_by"></b>
              </div>
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"></label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" v-if="vm_tab1.cancelled_remarks && vm_tab1.cancelled_remarks != ' '">
                Reason: <b v-text="vm_tab1.cancelled_remarks"></b>
              </div>
            </div>
            <div class="form-group m-form__group row" id="hrnoted">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Noted By </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.hr_noted_by"></b>
              </div>
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"></label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" v-if="vm_tab1.hr_noted_remarks && vm_tab1.hr_noted_remarks != ' '">
                HR Remarks: <b v-text="vm_tab1.hr_noted_remarks"></b>
              </div>
            </div>
          </div>
        </div>
        <div class="m-separator m-separator--dashed d-xl-12"></div>
        <br>
        <div class="row">
          <div class="col-md-6"> 
            <div class="form-group m-form__group row" id="receive">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Employee's Name </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.ref_yr"></b>
              </div>
            </div>
            <div class="form-group m-form__group row">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Company </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.company"></b><br>
                <span v-text="vm_tab1.department"></span>
              </div>
            </div>
            <br><br>
            <div class="form-group m-form__group row">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Reason for Leave </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" style="word-wrap: break-word">
                <b v-text="vm_tab1.reason"></b>
              </div>
            </div>
            <div class="form-group m-form__group row">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Address on Leave </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" style="word-wrap: break-word">
                <b v-text="vm_tab1.address"></b>
              </div>
            </div>
            <div class="form-group m-form__group row">
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Number on Leave </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
                <b v-text="vm_tab1.phone"></b>
              </div>
            </div>
          </div>
          <div class="col-md-6">  
            <div class="form-group m-form__group row" >
              <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Type </label>
              <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                <b v-text="vm_tab1.type"></b>
              </div>
          </div>
          <div class="form-group m-form__group row" >
            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Date </label>
            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
              <b v-text="vm_tab1.date_from"></b>
            </div>
          </div>
          <div class="form-group m-form__group row">
            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Duration </label>
            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
              <b v-text="vm_tab1.ref_month"></b>
            </div>
          </div>
          <br>
          <div class="form-group m-form__group row">
            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Nature of Leave </label>
            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
              <b v-text="vm_tab1.nature"></b>
            </div>
          </div>
          <div class="form-group m-form__group row" id="leave">
            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> Leave Pay </label>
            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12">
              <b v-text="vm_tab1.hr_noted_pay"></b>
            </div>
          </div>
          <!--<div class="form-group m-form__group row" id="hr_remark">
            <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12"> HR Remarks </label>
            <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
              <b v-text="vm_tab1.hr_noted_remarks"></b>
            </div>
          </div> -->
        </div>
      </div>
      <br>
      <?php $this->current_action = $this->core_layout->getCurrentActions();?>
      <div id="fix-mobile" class="modal-footer"> 
        <div class="row">
          <?php if((in_array("approve_action", $this->current_action))): ?>
            <button class="btn btn-success btnApprove_action text-white" id="btnapprove" onclick="open_approve()"> Approve </button>
          <?php endif; ?>
          <?php if((in_array("disapprove_action", $this->current_action))): ?>
            <button class="btn btn-danger btnDisapprove_action text-white" id="btndisapprove" onclick="open_dis()"> Disapprove </button>
          <?php endif; ?>
          <?php if((in_array("edit", $this->current_action))): ?>
            <button class="btn btn-warning btnEdit text-white" id="btnedit" onclick="edit_loa()"> Edit </button>
          <?php endif; ?>
          <?php if((in_array("cancel", $this->current_action))): ?>
            <button class="btn btn-danger btnCancel text-white" id="btncancel" onclick="open_cancel()"> Cancel </button>
          <?php endif; ?>
          <?php if((in_array("hr_note", $this->current_action))): ?>
            <button class="btn btn-focus btnHr_note text-white" id="btnnote" onclick="open_note()"> Note </button>
          <?php endif; ?>
          <?php if((in_array("undo_approval", $this->current_action))): ?>
            <button class="btn btn-danger btnUndo_approval text-white" id="btnundoapprove" onclick="open_undo_approve()"> Undo Approval </button>
          <?php endif; ?>
          <?php if((in_array("undo_disapproval", $this->current_action))): ?>
            <button class="btn btn-danger btnUndo_disapproval text-white" id="btnundodisapprove" onclick="open_undo_disapprove()"> Undo Disapproval </button>
          <?php endif; ?>
          <?php if((in_array("restore", $this->current_action))): ?>
            <button class="btn btn-success btnRestore text-white" id="btnundocancel" onclick="open_restore()"> Restore </button>
          <?php endif; ?>
          <?php if((in_array("undo_hr_note", $this->current_action))): ?>
            <button class="btn btn-danger btnUndo_hr_note text-white" id="btnundonote" onclick="open_undo_note()"> Undo HR Note </button>
          <?php endif; ?>
          <?php if((in_array("print", $this->current_action))): ?>
            <button class="btn btn-primary btnPrint text-white" id="btnprint" onclick="print()"> Print </button>
          <?php endif; ?>
          <?php if((in_array("back", $this->current_action))): ?>
            <!-- <button class="btn btn-metal btnBack text-white" id="btnback"> </button> -->
            <!-- <button class="btn btn-metal btnBack text-white" id="btnback2"> </button> -->
            <a class="btn btn-metal btnBack text-white" id="btnback" href="<?php echo site_url("eforms/loa/masterfile");?>"> BACK </a>
            <a class="btn btn-metal btnBack text-white" id="btnback2" href="<?php echo site_url("eforms/loa/archive_loa");?>"> BACK </a>
          <?php endif; ?>

        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group m-form__group row">
            <div class="m_datatable  m-datatable--default  m-datatable--scroll col-12" style="overflow-x: scroll;">
              <table class="table table-striped table-bordered" id="table-previous" width="100%">
                <thead>
                  <tr>
                    <th>Nature</th>
                    <th>Type</th>
                    <th>Duration</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div> 
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal_form_restore" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="#" id="form_restore" class="form-horizontal">
          <div class="modal-body">
            <input type="hidden" value="" name="restore_id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <div class="col-md-12">
                <b>Are you sure you want to Restore?</b> 
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="restore()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave" >yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">No</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->

  <div class="modal fade" id="modal_form_undo_approve" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="#" id="form_undo_approve" class="form-horizontal">
          <div class="modal-body">
              <input type="hidden" value="" name="undo_approve_id"/> 
              <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
              <div class="form-group">
                <div class="col-md-12">
                  <b>Are you sure you want to Undo Approval?</b> 
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="undo_approve()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave" >Yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">No</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->

  <div class="modal fade" id="modal_form_undo_disapprove" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="#" id="form_undo_disapprove" class="form-horizontal">
          <div class="modal-body">
            <input type="hidden" value="" name="undo_disapprove_id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <div class="col-md-12">
                <b>Are you sure you want to Undo Disapproval?</b> 
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="undo_disapprove()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave" >Yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">No</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->

  <div class="modal fade" id="modal_form_undo_note" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="#" id="form_undo_note" class="form-horizontal">
          <div class="modal-body ">
            <input type="hidden" value="" name="undo_note_id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <div class="col-md-12">
                <b>Are you sure you want to Undo Note?</b> 
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" onclick="undo_note()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave" >Yes</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">No</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->

  <div class="modal fade" id="modal_form_approve" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <form action="#" id="form_approve" class="form-horizontal">
          <div class="modal-body ">
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

            <div class="form-group">
              <label class="control-label col-md-2 required">Remarks</label>
              <div class="col-md-12">
                <textarea name="approved_remarks"  class="form-control" data-validation="required"> </textarea> 
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <!-- <button type="button" id="btnSave" onclick="approve()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button> -->
            <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->
  
  <div class="modal fade" id="modal_form_disapprove" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <form action="#" id="form_disapprove" class="form-horizontal">
          <div class="modal-body ">
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <label class="control-label col-md-2 required">Remarks</label>
              <div class="col-md-12">
                <textarea name="disapproved_remarks"  class="form-control" data-validation="required"> </textarea> 
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <!-- <button type="submit" id="btnSave" onclick="disapprove()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button> -->
            <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon btnClose" style="color: #FFFFFF;" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->

  <div class="modal fade" id="modal_form_cancel" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="#" id="form_cancel" class="form-horizontal">
          <div class="modal-body ">
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <label class="control-label col-md-2 required">Reason</label>
              <div class="col-md-12">
              <textarea name="cancelled_remarks"  class="form-control" data-validation="required"> </textarea> 
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <!-- <button type="submit" id="btnSave" onclick="cancel()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button> -->
            <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" style="color: #FFFFFF;" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->

  <div class="modal fade" id="modal_form_noted" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content form">
        <div class="modal-header">
          <h3 class="modal-title"></h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <form action="#" id="form_noted" class="form-horizontal">
          <div class="modal-body">
            <input type="hidden" value="" name="id"/> 
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
              <label class="control-label col-md-4 required">Leave Pay</label>
              <div class="col-md-12">
                <select id="hr-noted-pay" name="hr_noted_pay" class="form-control" data-validation="required">
                  <option></option>
                  <option>With Pay</option>
                  <option>Without Pay</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-2 required">Notes</label>
              <div class="col-md-12">
                <textarea name="hr_noted_remarks"  class="form-control" data-validation="required"> </textarea> 
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <!-- <button type="submit" id="btnSave" onclick="note()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button> -->
            <button type="submit" id="btnSave" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnSave">Save</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnClose" data-dismiss="modal">Close</button>
          </div>
        </form>
      </div><!-- /.modal-content -->
    </div>
  </div>
  <!-- </div> -->
</div>