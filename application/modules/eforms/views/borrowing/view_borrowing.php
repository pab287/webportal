<style>
  @media screen and (max-width: 690px){
      #fix-mobile{
        display: flex;
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
                  <a type="button" href="masterfile" title="Go to Masterfile" class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                      <i class="la la-arrow-left"></i>
                  </a>
              </span>
							<h3 class="m-portlet__head-text">
								View Borrowing
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools"></div>
				</div>
				<div class="m-portlet__body" id="BorrowingDataRenderer">
					<div class="row">
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Borrowing Form No.
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12"><b><p id="ref_no" v-text="vm_tab1.reference_no">&nbsp;</p></b></div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  File Under
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="source">
                 <b v-text="vm_tab1.company"></b><br>
                 <span v-text="vm_tab1.department"></span>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Borrower
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="from">
                  <p class="m--marginless" v-text="vm_tab1.borrower">&nbsp;</p>
                  <p v-text="vm_tab1.position">&nbsp;</p>
                </div>
              </div>
              <div class="form-group m-form__group row">
                <label class="col-3">
                  Status
                </label>
                <div class="col-9">
                  
                  <span class="m-badge text-white m-badge--wide font-weight-bold" :class="class_name" role="alert" v-text="vm_tab1.status"></span>
                </div>
              </div>
              <div class="form-group m-form__group row" id="other_remark">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Purpose
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b><p v-text="vm_tab1.purpose">&nbsp;</p></b>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12">
              <div class="form-group m-form__group row" >
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Created By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b><p v-text="vm_tab1.created_by">&nbsp;</p></b>
                </div> 
              </div>
              <div class="form-group m-form__group row" >
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Last Edited By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="edited">
                  <b><p v-text="vm_tab1.last_edited_by">&nbsp;</p></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="approve">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Approved By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b><p v-text="vm_tab1.approved_by">&nbsp;</p></b>
                </div>
              </div>     
              <div class="form-group m-form__group row" id="cancel">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
                  Cancelled By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b><p v-text="vm_tab1.cancelled_by">&nbsp;</p></b>
                </div>
              </div>
             <div class="form-group m-form__group row" id="cancel_remark">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                  Reason
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b><p  class="m--margin-top-5" v-text="vm_tab1.cancelled_remarks">&nbsp;</p></b>
                </div>
              </div>  
              <div class="form-group m-form__group row" id="release">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                  Released By
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" >
                  <b><p  class="m--margin-top-5" v-text="vm_tab1.released_by">&nbsp;</p></b>
                </div>
              </div>
              <div class="form-group m-form__group row" id="release_remark">
                <label class="col-md-3 col-lg-3 col-sm-3 col-xs-12 col-form-label">
                  Remarks
                </label>
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12" id="remark">
                  <b><p  class="m--margin-top-5" v-text="vm_tab1.released_remarks">&nbsp;</p></b>
                </div>
              </div> 
            </div>         
          </div>
          <div class="m-separator m-separator--dashed d-xl-12"></div> 
          <br>
          <div class="row" id="borrowing_details">
            <div class="col-md-12">
              <div class="form-group m-form__group row">
                <div class="m_datatable  m-datatable--default  m-datatable--scroll table-responsive col-12">
                  <table class="table table-striped table-bordered" id="table-content" width="100%">
                    <thead>
                      <tr>
                        <th width="35%">Item Borrowed</th>
                        <th width="10%">Qty</th>
                        <th width="10%">Date Borrowed</th>
                        <th width="10%">Date Due</th>
                        <th width="10%">Date Returned</th>
                        <th width="25%">Reason for Extension</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="vm_contents in vm_tab_contents.data">
                        <td v-html="vm_contents.asset">
                        </td>
                        <td>{{ vm_contents.pieces }}</td>
                        <td>{{ vm_contents.date_borrowed }}</td>
                        <td>{{ vm_contents.date_due }}</td>
                        <td>{{ vm_contents.date_returned }}</td>
                        <td>{{ vm_contents.due_reason }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div> 
              </div>
            </div>
          </div>
          <div id="fix-mobile" class="modal-footer">
            <?php $tempActions = $this->core_layout->getCurrentActions(); ?>
            <?php if(in_array("approve_action", $tempActions)): ?>
              <a class="btn btn-success m-btn--custom btnApprove_action text-white m--hide" id="btnapprove" onclick="approve()"><span>APPROVE</span></a>      
            <?php endif; ?>
            <?php if(in_array("edit", $tempActions)): ?>
              <a class="btn btn-warning m-btn--custom btnEdit text-white m--hide" id="btnedit" onclick="edit_borrowing()"><span>EDIT</span></a>
            <?php endif; ?>
            <?php if(in_array("cancel_action", $tempActions)): ?>
              <a class="btn btn-danger m-btn--custom btnCancel_action text-white m--hide" id="btncancel" onclick="open_cancel()"><span>CANCEL</span></a>
            <?php endif; ?>
            <?php if(in_array("acct_release", $tempActions)): ?>
              <a class="btn btn-focus m-btn--custom btnAcct_release text-white m--hide" id="btnrelease" onclick="open_release()"><span>RELEASE</span></a>
            <?php endif; ?>
            <?php if(in_array("undo_approval", $tempActions)): ?>
              <a class="btn btn-danger m-btn--custom btnUndo_approval text-white m--hide" id="btnundoapprove" onclick="undo_approve()"><span>UNDO APPROVE</span></a>            
            <?php endif; ?>
            <?php if(in_array("restore", $tempActions)): ?>
              <a class="btn btn-success m-btn--custom btnRestore text-white m--hide" id="btnundocancel" onclick="restore()"><span>RESTORE</span></a>
            <?php endif; ?>
            <?php if(in_array("undo_release", $tempActions)): ?>
              <a class="btn btn-danger m-btn--custom btnUndo_release text-white m--hide" id="btnundorelease" onclick="undo_release()"><span>UNDO RELEASE</span></a>
            <?php endif; ?>
            <?php if(in_array("print", $tempActions)): ?>
              <a class="btn btn-primary m-btn--custom btnPrint text-white m--hide" id="btnprint" onclick="printArea()"><span>PRINT</span></a>
            <?php endif; ?>
            <a href="<?php echo site_url("eforms/borrowing/masterfile");?>" class="btn btn-metal m-btn--custom text-white btnBack m-animate-fade-in" id="btnback"><span>BACK</span></a>
            <a href="<?php echo site_url("eforms/borrowing/archive_borrowing");?>" class="btn btn-metal m-btn--custom text-white btnArchive m--hide" id="btnback2"><span>BACK</span></a>
          </div>
				</div>
			</div>
    </div>
  </div>
</div>
<!--end::Portlet-->

<!--modal approve -->
<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Approve Borrowing
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
                            Do you want to undo approval of this form?
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

<div class="modal fade" id="modal_form_cancel" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="#" id="form_cancel" class="form-horizontal">
      <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <div class="modal-body form">
          <input type="hidden" value="" name="id"/> 
          <div class="form-group">
            <label class="control-label col-md-2 col-sm-2 col-xs-12">Reason</label>
            <div class="col-md-12">
              <textarea name="cancelled_remarks"  class="form-control" data-validation="required"> </textarea> 
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnSave" onclick="cancel()" class="btn btn-primary m-btn m-btn--icon  btnNew">Save</button>
          <button type="button" class="btn btn-metal text-white m-btn m-btn--icon  btnNew" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

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

<div class="modal fade" id="undo_release_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: block;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">
                    Undo Release Form
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form id = "undo_release_form">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <div class="col-12 modal-body">
                    <div class="form-group m-form__group row">
                        <label class="col-12 col-form-label form-control-label">
                            Do you want to undo releasing of this form?
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

<div class="modal fade" id="modal_form_released" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"></h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
      <div class="modal-body form">
        <form action="#" method="POST" id="form_released" class="form-horizontal">
          <input type="hidden" value="" name="id"/> 
          <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">

          <div class="form-group">
          <label class="control-label col-md-2 col-sm-2 col-xs-12">Remarks</label>
          <div class="col-md-12">
            <textarea name="released_remarks"  class="form-control" data-validation="required"> </textarea> 
          </div>
          </div>
          
          </div>
          <div class="modal-footer">
            <button type="button" id="btnSave" onclick="released()" class="btn btn-primary m-btn m-btn--custom m-btn--icon  btnNew">Release</button>
            <button type="button" class="btn btn-metal text-white m-btn m-btn--custom m-btn--icon  btnNew" data-dismiss="modal">Cancel</button>
          </div>
          </form>
        </div><!-- /.modal-content -->
      </div>
</div>

<div class="m-content" hidden>
	<div class="m-portlet__body" id="printableArea">
    <div class="row">
      <div class="col-xs-12 table-responsive" style="text-transform: uppercase;">
        <table style="font-size:small;" width="100%" border="0">
          <tr>
            <td width="50%"><h2><div id="company"></div></h2></td>
            <td align="right" width="25%"><h2><small>CONTROL #</small></h2></td>
            <td align="right" width="25%"><h2><div v-text="vm_tab_main.reference_no"></div></h2></td>
          </tr>
          <tr>
            <td><!--Carlos Hilado Ave., Circumferencial Road, Brgy. Bata, Bacolod City, Negros Occ.--></td>
          </tr>
        </table>
        <table style="font-size:small" width="100%">
        	<tr align="center">
                <td width="100%" ><b>B O R R O W I N G &nbsp;&nbsp; A G R E E M E N T &nbsp;&nbsp; F O R M &nbsp;&nbsp;(BF)</b></td>
            </tr>
        </table>
        <hr style="margin:10px 0px 3px 0px;border-top: 1px dashed black;">
        <table style="font-size:small" width="100%">
          <tr>
            <td>ISSUED TO : <b v-text="vm_tab_main.borrower"></b></td>
            <td align="right">DATE ISSUED : <b v-text="vm_tab_main.date_trans"></b></td>
          </tr>
          <tr>
            <td colspan="2">DEPARTMENT : <b v-text="vm_tab_main.department"></b></td>
          </tr>
        </table>
        <br>
        <table style="font-size:small; width: 100%">
            <tr>
                <td width="100%">
                    <p>&emsp;This is to acknowledge receipt of the following item/s which will be under my temporary accountability. I also acknowledge that I shall only be released from the said accountability upon return of the said item to the Property Custodian on the due date specified below.</p>
                </td>
              </tr>
        </table>
        <br>
        <hr style="margin:0px 0px 5px 0px;border-top: 1px dashed black;">
        <table style="font-size:small; width: 100%" cellspacing="2">
        	<tr>
            <td align="center" width="15%"><label>ASSET CODE</label></td>
            <td align="center" width="35%"><label>ASSET NAME</label></td>
            <td align="center" width="15%"><label>BRAND/MODEL</label></td>
            <td align="center" width="10%"><label>QTY</label></td>
            <td align="center" width="15%"><label>DATE DUE</label></td>
            <td align="right" width="10%"><label>AMOUNT</label></td>
          </tr>
          <tbody>
            <tr v-for="vm_contents_print in vm_tab_contents.data">
              <td align="center" v-html="vm_contents_print.asset_code">
              </td>
              <td align="center">{{ vm_contents_print.asset_name }}</td>
              <td align="center">{{ vm_contents_print.brand_model }}</td>
              <td align="center">{{ vm_contents_print.pieces }}</td>
              <td align="center">{{ vm_contents_print.date_due }}</td>
              <td align="right">{{ vm_contents_print.amount }}</td>
            </tr>
          </tbody>
       	</table>
        <hr style="margin:0px;border-top: 1px solid black;">
        <table id="tbody" style="font-size:small; width: 100%">
        </table>
        <hr style="margin:5px 0px 5px 0px;border-top: 1px solid black;">
        <table id="tbody1" style="font-size:small; width: 100%">
          <tr>
            <td colspan="2"></td>
            <td align="right" width="28%"><b>TOTAL</b></td>
            <td align="right" width="15%"><b id="total">{{ vm_tab_main.total_amount }}</b></td>
			    </tr>
        </table>
        <table style="font-size:small; width: 100%">
          <!--tr>
              <td align="center" width="100%">
                  <p><strong>Warehouse Policy</strong></p>
                  <p><strong>"All items borrowed anytime during the week must be returned on Saturday."<strong></p>
                </td>
            </tr-->
        	<tr>
            	<td width="100%">
                	<p>&emsp;I understand that all items listed are for official use only. Furthermore, I hereby agree to the following provisions:</p>
                  <ul>
                    <li>I shall inform the Property Custodian in case of lost/damage item and will pay the company the full replacement cost or value of any lost or damaged items and will allow the company to deduct it from my salary.</li>
                    <li>I will return all borrowed items on or before the due date. Otherwise, I shall inform the Property Custodian for an extension of the return date.</li>
                    <li>I acknowledge that failure to return the borrowed item (s) is a ground for disciplinary action.</li>
            </ul>
                </td>
            </tr>
        </table>
        <table style="font-size:small; width: 100%; margin-top: 10px" border="0">
        	<tr>
            	<td width="25%" colspan="2">Issued by :</td>
                <td width="25%" colspan="2" style="margin:0px 0px 0px 5px">Received by :</td>
                <td width="25%" colspan="2" style="margin:0px 0px 0px 5px">Returned by :</td>
                <td width="25%" colspan="2" style="margin:0px 0px 0px 5px">Acknowledge by :</td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
        	<tr>
            	<td width="25%" colspan="2" align="left"><label id="">{{ vm_tab_main.created_by_print }}</label></td>
                <td width="25%" colspan="2" align="left"><label id="">{{ vm_tab_main.borrower }}</label></td>
                <td width="25%" colspan="2" align="center"><label id=""></label></td>
                <td width="25%" colspan="2" align="center"><label id=""></label></td>
            </tr>
            <tr>
            	<td colspan="2"><hr style="margin:0px 5px 0px 0px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
                <td colspan="2"><hr style="margin:0px 0px 0px 5px;border-top: 1px dashed black;"></td>
            </tr>
        </table>
        
      </div>
      <!-- /.col -->
    </div>
	</div>
</div>