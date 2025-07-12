<div class="m-content">
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <span class="m-portlet__head-icon">
                                <a type="button" href="payment" title="Go to Masterfile"
                                    class="btn btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill btnBack">
                                    <i class="la la-arrow-left"></i>
                                </a>
                            </span>
                            <h3 class="m-portlet__head-text">
                                Create Payment
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <form id="frmCreatePayment" method="POST" class="m-form m-form--group-seperator-dashed" action="<?php echo site_url('eforms/billing/create_new_payment')?> ">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" id="serialize_penalties" name="penalties" value="">
                    <input type="hidden" id="is_penalty" name="is_penalty" value="">
                    <input type="hidden" id="reconnection_fee" name="reconnection_fee" value="">
                    <div class="m-portlet__body">
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10">
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    Account Information
                                </h3>
                            </div>
                            <div class="row m--margin-bottom">
                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Select Account</label>
                                        <select name="account_id" class="form-control m-input" id="accountSelect" data-validation="required">
                                        
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-group form__group" >
                                    <label>Account Name</label>
                                    <input class="form-control m-input customer_name" readonly type="text">
                                </div>             
                                </div>  
                                <div class="col-md-4" id="previous_payment" style="border-left: 1px solid #d9d9d9;">       
                                Previous Payments
                                <div class="payment_list">
                                </div>
                                </div>  
                            </div>  
                        </div>
                        <hr>
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10 row">
                            <div class="m-form col-md-6 form__group" style=" border-right: 1px solid #d9d9d9;">
                                <div class="m-form__heading">
                                    <h3 class="m-form__heading-title">
                                        Payment Information
                                    </h3>
                                </div> 
                                <div class="row m--margin-bottom">
                                    <div class="col-md-6">
                                        <div class="form-group form__group">   
                                            <label>Payment Type</label>
                                            <select name="payment_type" class="form-control m-input" id="payment_type" data-validation="required">
                                                <option value="">Select an option</option>
                                                <option value="cash">Cash</option>
                                                <option value="check">Check</option>
                                                <option value="bank">Bank</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 details_layout">
                                        <div class="form-group form__group">
                                            <label id="label_details">Details</label>
                                            <input class="form-control m-input payment_details" step="0.01" name="payment_details" autocomplete="off" data-validation="required">
                                        </div>             
                                    </div>
                                </div>
                                <div class="row m--margin-bottom">
                                    <div class="col-md-6">
                                        <div class="form-group form__group">   
                                            <label>Select Bill</label>
                                            <select name="bill_id" class="form-control m-input" id="billSelect" data-validation="required">
                                            </select>
                                        </div>             
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="form-group form__group">   
                                            <label>Bill Amount</label>
                                            <input class="form-control m-input text-right billing_amount" step="0.01" id="billing_amount" readonly autocomplete="off">
                                        </div>             
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="m-form col-md-6 form__group balance_layout" style="display: none;">
                                <div class="m-form__heading">
                                    <h3 class="m-form__heading-title">
                                        Over Payment Balance
                                    </h3>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <div class="form-group form__group">
                                            <label>Total</label>
                                            <input class="form-control m-input text-right total_balance" step="0.01" readonly type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="col-md-12 remaining_balance_layout">
                                        <div class="form-group form__group">
                                            <label>Remaining</label>
                                            <input class="form-control m-input text-right remaining_balance" name="balance" step="0.01" readonly type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-form m-form--label-align-right m--margin-top-10 m--margin-bottom-10 row">
                        <div class="m-form col-md-6 form__group" style=" border-right: 1px solid #d9d9d9;">
                                <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    &nbsp;
                                </h3>
                            </div>
                            <div class="row m--margin-bottom">
                                <div class="col-md-6">
                                    <div class="form-group form__group">   
                                        <label>Payment Date</label>
                                        <div class="input-group date" id="m_datepicker_payment_date">
                                            <input type="text" class="form-control m-input" readonly name="payment_date" placeholder="Select date" data-validation="required">
                                            <span class="input-group-addon">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>             
                                </div>  
                                <div class="col-md-6">
                                    <div class="form-group form__group">   
                                        <label id="receive_text">Received Amount</label>
                                        <input id="receivedAmount" type="text" class="form-control m-input text-right" name="received_amount" autocomplete="off" data-validation="required" oninput="handleValueChange()" style="border-color: black;"><small>Amount should not be less than or equal to 0.</small>
                                    </div>             
                                </div>
                            </div>
                        </div>
                        <div class="m-form col-md-6 form__group row penalty_layout" style="display: none;">
                            <div class="col-md-12 m--margin-bottom">
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">
                                    Penalty Details
                                </h3>
                            </div>
                            <div class="row m--margin-bottom penalty_details">
                                
                            </div>
                            </div>
                        </div>
                        </div>
                            
                            <hr>
                            <!-- <div class="row m--margin-bottom">
                                <div class="col-md-4">
                                    <div class="form-group form__group">
                                        <label id="maintenanance_fee_title" style="text-decoration:line-through;">Maintenance Fee</label>
                                        <input id="maintenanance_fee" type="text" readonly class="form-control m-input text-right" step="any" autocomplete="off" style="background-color: #efefef;">
                                    </div>             
                                </div>  
                                <div class="col-md-4">
                                    <span class="input-group m-switch m-switch--outline m-switch--icon m-switch--success" style="margin-top: 24px;">
                                        <label>
                                            <input type="checkbox" id="maintenanance_toggle">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div> -->
                            <div class="row m--margin-bottom">
                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Sub Total</label>
                                        <input id="sub_total" name="sub_total" type="text" readonly class="form-control m-input text-right" step="any" autocomplete="off">
                                    </div>             
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label>Balance Covered</label>
                                        <input id="balance_covered" type="text" name="balance_covered" readonly class="form-control m-input text-right" step="any" autocomplete="off">
                                    </div>             
                                </div>  

                                <div class="col-md-4">
                                    <div class="form-group form__group">   
                                        <label style="font-weight: bold;">
                                            Net Payment
                                        </label>
                                        <input class="form-control m-input net_payment text-right" id="net_payment" name="net_payment" readonly type="text" style="font-weight: bold;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <div class="m-portlet__foot">
                        <div class="m-form__actions m--paddingless text-right" style="padding:0;">
                            <button type="submit" class="btn btn-accent btnSave" id="btnSave" disabled>
                                Submit
                            </button>
                            <a href="<?php echo site_url("eforms/billing/payment"); ?>">
                                <button type="button" class="btn btn-secondary btnCancel">
                                    Cancel
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="ar_modal" tabindex="-1">
	<div class="modal-dialog modal-dialog-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header" id="">
                <h5>Acknowledgement Receipt</h5>
			</div>
            <div class="col-12 modal-body">
				<h3>Reference #: <i><span class='m--font-boldest' id="ar_code"></span></i></h3>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>